<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ChronicDiseaseResource;
use App\Http\Resources\Api\ComplaintResource;
use App\Http\Resources\Api\DiagnosisResource;
use App\Http\Resources\Api\DoctorNoteResource;
use App\Http\Resources\Api\DoctorOrderResource;
use App\Http\Resources\Api\DoctorOrderReplyResource;
use App\Http\Resources\Api\RoomMedicationResource;
use App\Http\Resources\Api\RoomReportAnswerResource;
use App\Http\Resources\Api\RoomReportResource;
use App\Http\Resources\Api\RoomResource;
use App\Http\Resources\Api\UserResource;
use App\Http\Resources\Api\VisitFormResource;
use App\Http\Traits\ApiResponse;
use App\Models\ChronicDisease;
use App\Models\Complaint;
use App\Models\Diagnosis;
use App\Models\DocumentTemplate;
use App\Models\DoctorNote;
use App\Models\DoctorOrder;
use App\Models\DoctorOrderReply;
use App\Models\Room;
use App\Models\RoomAttachment;
use App\Models\RoomMedication;
use App\Models\RoomMember;
use App\Models\RoomReport;
use App\Models\RoomReportAnswer;
use App\Models\User;
use App\Models\VisitForm;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class SihatiController extends Controller
{
    use ApiResponse;

    public function __construct(private FirebaseService $firebase)
    {
    }

    /**
     * Verify that the authenticated user is the patient of the room
     * or an active member of the room. Aborts 403 if not.
     */
    private function verifyRoomAccess(Room $room): void
    {
        $userId = auth('user-api')->id();

        if ($room->patient_id === $userId) {
            return;
        }

        $isMember = $room->members()->where('user_id', $userId)->exists();

        if (!$isMember) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الغرفة');
        }
    }

    /**
     * True when the given user is a room_members row of the given role
     * for this room. Used to gate role-restricted write actions.
     */
    private function hasRoomRole(Room $room, int $userId, string $role): bool
    {
        return $room->members()->where('user_id', $userId)->where('role', $role)->exists();
    }

    /**
     * GET /sihati/my-room — active room for the authenticated patient.
     */
    public function myRoom(Request $request)
    {
        $user = $request->user('user-api');

        $room = Room::where('patient_id', $user->id)
            ->where('is_active', true)
            ->with(['patient', 'members.user'])
            ->first();

        if (!$room) {
            return $this->error('لا يوجد غرفة نشطة', null, 404);
        }

        return $this->success(new RoomResource($room));
    }

    /**
     * GET /sihati/items — paginated feed mixing chat rooms and medical
     * visit forms, scoped to what the caller created or is a member of.
     */
    public function items(Request $request)
    {
        $user = $request->user('user-api');
        $perPage = 15;
        $page = max(1, (int) $request->input('page', 1));

        $rooms = Room::with('patient')
            ->where(function ($q) use ($user) {
                $q->where('created_by', $user->id)
                    ->orWhere('patient_id', $user->id)
                    ->orWhereHas('members', fn ($m) => $m->where('user_id', $user->id));
            })
            ->get()
            ->map(fn (Room $room) => [
                'id'               => $room->id,
                'item_type'        => 'chat_room',
                'title'            => $room->name,
                'patient_name'     => optional($room->patient)->name,
                'room_id'          => $room->id,
                'firebase_room_id' => $room->firebase_room_id,
                'created_at'       => $room->created_at,
            ]);

        $visitForms = VisitForm::with('patient')
            ->where(function ($q) use ($user) {
                $q->where('submitted_by', $user->id)
                    ->orWhere('patient_id', $user->id);
            })
            ->get()
            ->map(fn (VisitForm $form) => [
                'id'               => $form->id,
                'item_type'        => 'medical_visit_form',
                'title'            => 'زيارة طبية: ' . optional($form->patient)->name,
                'patient_name'     => optional($form->patient)->name,
                'room_id'          => null,
                'firebase_room_id' => null,
                'created_at'       => $form->created_at,
            ]);

        $items = $rooms->concat($visitForms)->sortByDesc('created_at')->values();

        $paginator = new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return $this->success($paginator->toArray());
    }

    /**
     * POST /sihati/rooms — patient intake, super-nurse only.
     */
    public function storeRoom(Request $request)
    {
        $user = $request->user('user-api');

        if ($user->role !== 'super_nurse') {
            abort(403, 'فقط الممرض المسؤول يمكنه إنشاء غرفة');
        }

        $data = $request->validate([
            'assigned_user_id'       => 'required|exists:users,id',
            'name'                   => 'required|string|max:255',
            'room_name'              => 'required|string|max:255',
            'age'                    => 'required|integer|min:0|max:150',
            'gender'                 => 'required|in:male,female',
            'weight'                 => 'required|numeric|min:0',
            'has_allergies'          => 'required|boolean',
            'allergy_details'        => 'nullable|string',
            'marital_status'         => 'required|in:single,married,divorced,widowed',
            'functional_status'      => 'required|in:independent,partially_dependent,fully_dependent',
            'race'                   => 'required|in:white,black',
            'education_level'        => 'required|string|max:255',
            'blood_group'            => 'required|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'diagnosis_ids'          => 'sometimes|array',
            'diagnosis_ids.*'        => 'exists:diagnoses,id',
            'chronic_disease_ids'    => 'sometimes|array',
            'chronic_disease_ids.*'  => 'exists:chronic_diseases,id',
            'attachments'            => 'sometimes|array',
            'attachments.*'          => 'file|mimes:png,jpg,jpeg,pdf|max:10240',
        ]);

        // `room_name` is the room's own title (existing `rooms.name` column);
        // `name` is validated for contract parity with the app but has no
        // separate column of its own to persist into.
        $room = Room::create([
            'patient_id'         => $data['assigned_user_id'],
            'created_by'         => $user->id,
            'name'               => $data['room_name'],
            'age'                => $data['age'],
            'gender'             => $data['gender'],
            'weight'             => $data['weight'],
            'has_allergies'      => $data['has_allergies'],
            'allergy_details'    => $data['allergy_details'] ?? null,
            'marital_status'     => $data['marital_status'],
            'functional_status'  => $data['functional_status'],
            'race'               => $data['race'],
            'education_level'    => $data['education_level'],
            'blood_group'        => $data['blood_group'],
            'is_active'          => true,
        ]);

        if (!empty($data['diagnosis_ids'])) {
            $room->diagnoses()->sync($data['diagnosis_ids']);
        }

        if (!empty($data['chronic_disease_ids'])) {
            $room->chronicDiseases()->sync($data['chronic_disease_ids']);
        }

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                RoomAttachment::create([
                    'room_id'       => $room->id,
                    'uploaded_by'   => $user->id,
                    'file_path'     => $file->store('rooms/attachments', 'public'),
                    'original_name' => $file->getClientOriginalName(),
                ]);
            }
        }

        RoomMember::firstOrCreate(
            ['room_id' => $room->id, 'user_id' => $user->id],
            ['role' => 'super_nurse']
        );

        RoomMember::firstOrCreate(
            ['room_id' => $room->id, 'user_id' => $room->patient_id],
            ['role' => 'patient']
        );

        $room->update(['firebase_room_id' => $this->firebase->createRoomDocument($room)]);
        $this->firebase->syncRoomMembers($room);

        $room->load(['patient', 'members.user', 'diagnoses', 'chronicDiseases', 'attachments']);

        return $this->success(new RoomResource($room), 'تم إنشاء الغرفة بنجاح', 201);
    }

    /**
     * GET /sihati/users/search — patient-linkage picker.
     */
    public function searchUsers(Request $request)
    {
        $query = trim((string) $request->input('query', ''));

        if ($query === '') {
            return $this->success([]);
        }

        $users = User::where('name', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->limit(20)
            ->get();

        return $this->success(UserResource::collection($users));
    }

    /**
     * GET /sihati/diagnoses — admin-managed searchable checklist.
     */
    public function diagnoses(Request $request)
    {
        $query = Diagnosis::active();

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        return $this->success(DiagnosisResource::collection($query->get()));
    }

    /**
     * GET /sihati/chronic-diseases — admin-managed searchable checklist.
     */
    public function chronicDiseases(Request $request)
    {
        $query = ChronicDisease::active();

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        return $this->success(ChronicDiseaseResource::collection($query->get()));
    }

    /**
     * GET /sihati/rooms/{id} — room detail with assignment template.
     */
    public function roomDetail(Request $request, int $id)
    {
        $room = Room::with([
            'patient', 'members.user', 'activeNurseAssignment.template.fields',
            'diagnoses', 'chronicDiseases', 'attachments',
        ])->findOrFail($id);

        $this->verifyRoomAccess($room);

        return $this->success(new RoomResource($room));
    }

    /**
     * GET /sihati/rooms/{id}/reports — paginated reports for room.
     */
    public function roomReports(Request $request, int $id)
    {
        $room = Room::findOrFail($id);
        $this->verifyRoomAccess($room);

        $reports = RoomReport::where('room_id', $room->id)
            ->with('submittedBy')
            ->orderByDesc('submitted_at')
            ->paginate(15);

        return $this->success(RoomReportResource::collection($reports)->response()->getData(true));
    }

    /**
     * POST /sihati/rooms/{id}/reports — submit a report.
     */
    public function submitReport(Request $request, int $id)
    {
        $room = Room::findOrFail($id);
        $this->verifyRoomAccess($room);

        $user = $request->user('user-api');

        if ($request->filled('report_hour')) {
            if (! $this->hasRoomRole($room, $user->id, 'nurse')) {
                abort(403, 'فقط الممرضون يمكنهم تعبئة التقرير الساعي');
            }

            $currentHour = now('Asia/Amman')->format('H:00');

            if ($request->report_hour !== $currentHour) {
                return $this->error('لا يمكن تعبئة تقرير لساعة غير الساعة الحالية', null, 422);
            }

            $alreadyFilled = RoomReport::where('room_id', $room->id)
                ->where('report_hour', $request->report_hour)
                ->exists();

            if ($alreadyFilled) {
                return $this->error('تم تعبئة تقرير هذه الساعة مسبقاً', null, 422);
            }
        }

        // Hourly nurse reports must resolve to the 'nurse' template specifically —
        // a room can have an active 'nurse' assignment and an active 'doctor'
        // assignment at the same time, so the generic activeAssignment() is
        // ambiguous here.
        $assignment = $request->filled('report_hour')
            ? $room->activeNurseAssignment()->with('template.fields')->first()
            : $room->activeAssignment()->with('template.fields')->first();

        if (!$assignment || !$assignment->template) {
            return $this->error('لا يوجد قالب تقرير مُعيَّن للغرفة', null, 422);
        }

        $template = $assignment->template;
        $fields   = $template->fields;

        // Validate required fields
        $answers = $request->input('answers', []);
        foreach ($fields as $field) {
            if ($field->is_required && empty($answers[$field->id])) {
                return $this->error(
                    'الحقل "' . $field->question . '" مطلوب',
                    null,
                    422
                );
            }
        }

        $report = RoomReport::create([
            'room_id'                    => $room->id,
            'report_template_id'         => $template->id,
            'room_template_assignment_id'=> $assignment->id,
            'submitted_by'               => $user->id,
            'report_type'                => $template->template_type,
            'submitted_at'               => now(),
            'report_hour'                => $request->input('report_hour'),
            'note'                       => $request->input('note'),
        ]);

        foreach ($fields as $field) {
            $rawAnswer = $answers[$field->id] ?? null;

            $answerData = [
                'room_report_id'          => $report->id,
                'report_template_field_id'=> $field->id,
                'field_question'          => $field->question,
                'field_answer_type'       => $field->answer_type,
                'sort_order'              => $field->sort_order,
            ];

            switch ($field->answer_type) {
                case 'text':
                    $answerData['answer_text'] = $rawAnswer;
                    break;

                case 'number':
                    $answerData['answer_number'] = is_numeric($rawAnswer) ? $rawAnswer : null;
                    break;

                case 'yes_no':
                    $answerData['answer_boolean'] = filter_var($rawAnswer, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                    break;

                case 'image':
                    if ($request->hasFile("answers.{$field->id}")) {
                        $path = $request->file("answers.{$field->id}")->store('room_reports', 'public');
                        $answerData['answer_image'] = $path;
                    }
                    break;
            }

            RoomReportAnswer::create($answerData);
        }

        $report->load('answers', 'submittedBy');

        return $this->success(new RoomReportResource($report), 'تم إرسال التقرير', 201);
    }

    /**
     * GET /sihati/rooms/{id}/reports/{reportId} — single report with answers.
     */
    public function reportDetail(Request $request, int $id, int $reportId)
    {
        $room = Room::findOrFail($id);
        $this->verifyRoomAccess($room);

        $report = RoomReport::where('room_id', $room->id)
            ->with('answers', 'submittedBy')
            ->findOrFail($reportId);

        return $this->success(new RoomReportResource($report));
    }

    /**
     * GET /sihati/rooms/{id}/doctor-notes — free-text medical notes.
     */
    public function doctorNotesIndex(Request $request, int $id)
    {
        $room = Room::findOrFail($id);
        $this->verifyRoomAccess($room);

        $notes = DoctorNote::where('room_id', $room->id)->with('doctor')->latest()->paginate(15);

        return $this->success(DoctorNoteResource::collection($notes)->response()->getData(true));
    }

    /**
     * POST /sihati/rooms/{id}/doctor-notes — doctor-only.
     */
    public function storeDoctorNote(Request $request, int $id)
    {
        $room = Room::findOrFail($id);
        $this->verifyRoomAccess($room);

        $user = $request->user('user-api');

        if (! $this->hasRoomRole($room, $user->id, 'doctor')) {
            abort(403, 'فقط الأطباء يمكنهم إضافة ملاحظة طبية');
        }

        $request->validate(['note' => 'required|string']);

        $note = DoctorNote::create([
            'room_id'   => $room->id,
            'doctor_id' => $user->id,
            'note'      => $request->note,
        ]);

        $note->load('doctor');

        return $this->success(new DoctorNoteResource($note), 'تم إضافة الملاحظة', 201);
    }

    /**
     * GET /sihati/rooms/{id}/doctor-orders — paginated doctor orders.
     */
    public function doctorOrders(Request $request, int $id)
    {
        $room = Room::findOrFail($id);
        $this->verifyRoomAccess($room);

        $orders = DoctorOrder::where('room_id', $room->id)
            ->with([
                'doctor'          => fn ($q) => $q->select('id', 'name'),
                'replies.nurse'   => fn ($q) => $q->select('id', 'name'),
            ])
            ->latest()
            ->paginate(15);

        return $this->success(DoctorOrderResource::collection($orders)->response()->getData(true));
    }

    /**
     * POST /sihati/rooms/{id}/doctor-orders — doctor issues an order.
     */
    public function storeDoctorOrder(Request $request, int $id)
    {
        $room = Room::findOrFail($id);
        $this->verifyRoomAccess($room);

        $user = $request->user('user-api');

        if (! $this->hasRoomRole($room, $user->id, 'doctor')) {
            abort(403, 'فقط الأطباء يمكنهم إصدار أمر طبي');
        }

        $request->validate(['order_text' => 'required|string']);

        $order = DoctorOrder::create([
            'room_id'     => $room->id,
            'doctor_id'   => $user->id,
            'order_text'  => $request->order_text,
            'is_executed' => false,
        ]);

        $order->load([
            'doctor'        => fn ($q) => $q->select('id', 'name'),
            'replies.nurse' => fn ($q) => $q->select('id', 'name'),
        ]);

        return $this->success(new DoctorOrderResource($order), 'تم إصدار الأمر الطبي', 201);
    }

    /**
     * POST /sihati/rooms/{id}/doctor-orders/{orderId}/reply — nurse replies to order.
     */
    public function replyOrder(Request $request, int $id, int $orderId)
    {
        $room = Room::findOrFail($id);
        $this->verifyRoomAccess($room);

        $user = $request->user('user-api');

        // Only nurses (room members with role=nurse) can reply
        $isNurse = $room->members()
            ->where('user_id', $user->id)
            ->where('role', 'nurse')
            ->exists();

        if (!$isNurse) {
            abort(403, 'فقط الممرضون يمكنهم الرد على الأوامر الطبية');
        }

        $request->validate([
            'reply_text'  => 'required|string',
            'is_executed' => 'required|boolean',
        ]);

        $order = DoctorOrder::where('room_id', $room->id)->findOrFail($orderId);

        DoctorOrderReply::create([
            'doctor_order_id' => $order->id,
            'nurse_id'        => $user->id,
            'reply_text'      => $request->reply_text,
        ]);

        if ($request->boolean('is_executed')) {
            $order->update(['is_executed' => true, 'executed_at' => now()]);
        }

        $order->load([
            'doctor'        => fn ($q) => $q->select('id', 'name'),
            'replies.nurse' => fn ($q) => $q->select('id', 'name'),
        ]);

        return $this->success(new DoctorOrderResource($order), 'تم إرسال الرد');
    }

    /**
     * GET /sihati/rooms/{id}/medications — all room medications.
     */
    public function roomMedications(Request $request, int $id)
    {
        $room = Room::findOrFail($id);
        $this->verifyRoomAccess($room);

        $medications = RoomMedication::where('room_id', $room->id)
            ->with('addedBy')
            ->latest()
            ->get();

        return $this->success(RoomMedicationResource::collection($medications));
    }

    /**
     * POST /sihati/rooms/{id}/medications — add a medication to the room.
     */
    public function addMedication(Request $request, int $id)
    {
        $room = Room::findOrFail($id);
        $this->verifyRoomAccess($room);

        $user = $request->user('user-api');

        $canAdd = $this->hasRoomRole($room, $user->id, 'super_nurse')
            || $this->hasRoomRole($room, $user->id, 'doctor');

        if (! $canAdd) {
            abort(403, 'فقط الممرض المسؤول أو الطبيب يمكنه إضافة دواء');
        }

        $request->validate([
            'medication_name' => 'required|string|max:255',
            'dosage'          => 'required|string|max:255',
            'frequency'       => 'required|string|max:255',
            'times'           => 'sometimes|array',
            'times.*'         => 'string',
            'start_date'      => 'required|date',
            'end_date'        => 'sometimes|nullable|date|after:start_date',
            'notes'           => 'sometimes|nullable|string',
        ]);

        $medication = RoomMedication::create([
            'room_id'         => $room->id,
            'added_by'        => $user->id,
            'medication_name' => $request->medication_name,
            'dosage'          => $request->dosage,
            'frequency'       => $request->frequency,
            'times'           => $request->input('times', []),
            'start_date'      => $request->start_date,
            'end_date'        => $request->end_date,
            'notes'           => $request->notes,
        ]);

        $medication->load('addedBy');

        return $this->success(new RoomMedicationResource($medication), 'تم إضافة الدواء', 201);
    }

    /**
     * POST /sihati/rooms/{id}/complaints — routed to the admin dashboard,
     * not visible in the chat itself.
     */
    public function storeComplaint(Request $request, int $id)
    {
        $room = Room::findOrFail($id);
        $this->verifyRoomAccess($room);

        $user = $request->user('user-api');

        $request->validate(['complaint_text' => 'required|string']);

        $complaint = Complaint::create([
            'room_id'        => $room->id,
            'patient_id'     => $room->patient_id,
            'submitted_by'   => $user->id,
            'complaint_text' => $request->complaint_text,
            'status'         => 'pending',
        ]);

        return $this->success(new ComplaintResource($complaint), 'تم إرسال الشكوى', 201);
    }

    /**
     * POST /sihati/rooms/{id}/chat-image — uploads a chat image, returns
     * its URL so the Flutter client can store it in the Firestore message.
     */
    public function uploadChatImage(Request $request, int $id)
    {
        $room = Room::findOrFail($id);
        $this->verifyRoomAccess($room);

        $request->validate(['image' => 'required|image|max:10240']);

        $path = $request->file('image')->store('rooms/chat', 'public');

        return $this->success(['url' => Storage::disk('public')->url($path)]);
    }

    /**
     * GET /sihati/documents/{type} — return document template (public).
     */
    public function getDocument(string $type)
    {
        if (!in_array($type, ['authorization', 'pledge'])) {
            return $this->error('نوع الوثيقة غير صالح', null, 422);
        }

        $document = DocumentTemplate::forType($type);

        return $this->success([
            'id'      => $document->id,
            'type'    => $document->type,
            'title'   => $document->title,
            'content' => $document->content,
        ]);
    }
}
