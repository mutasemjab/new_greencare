<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\DoctorOrderResource;
use App\Http\Resources\Api\DoctorOrderReplyResource;
use App\Http\Resources\Api\RoomMedicationResource;
use App\Http\Resources\Api\RoomReportAnswerResource;
use App\Http\Resources\Api\RoomReportResource;
use App\Http\Resources\Api\RoomResource;
use App\Http\Traits\ApiResponse;
use App\Models\DocumentTemplate;
use App\Models\DoctorOrder;
use App\Models\DoctorOrderReply;
use App\Models\Room;
use App\Models\RoomMedication;
use App\Models\RoomReport;
use App\Models\RoomReportAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SihatiController extends Controller
{
    use ApiResponse;

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
     * GET /sihati/rooms/{id} — room detail with assignment template.
     */
    public function roomDetail(Request $request, int $id)
    {
        $room = Room::with(['patient', 'members.user', 'activeAssignment.template'])
            ->findOrFail($id);

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

        $assignment = $room->activeAssignment()->with('template.fields')->first();

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

        $user = $request->user('user-api');

        $report = RoomReport::create([
            'room_id'                    => $room->id,
            'report_template_id'         => $template->id,
            'room_template_assignment_id'=> $assignment->id,
            'submitted_by'               => $user->id,
            'report_type'                => $template->template_type,
            'submitted_at'               => now(),
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
            'reply_text' => 'required|string',
        ]);

        $order = DoctorOrder::where('room_id', $room->id)->findOrFail($orderId);

        DoctorOrderReply::create([
            'doctor_order_id' => $order->id,
            'nurse_id'        => $user->id,
            'reply_text'      => $request->reply_text,
        ]);

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

        $request->validate([
            'medication_name' => 'required|string|max:255',
            'dosage'          => 'required|string|max:255',
            'frequency'       => 'required|string|max:255',
            'start_date'      => 'required|date',
            'end_date'        => 'sometimes|nullable|date|after:start_date',
            'notes'           => 'sometimes|nullable|string',
        ]);

        $medication = RoomMedication::create([
            'room_id'         => $room->id,
            'added_by'        => $request->user('user-api')->id,
            'medication_name' => $request->medication_name,
            'dosage'          => $request->dosage,
            'frequency'       => $request->frequency,
            'start_date'      => $request->start_date,
            'end_date'        => $request->end_date,
            'notes'           => $request->notes,
        ]);

        $medication->load('addedBy');

        return $this->success(new RoomMedicationResource($medication), 'تم إضافة الدواء', 201);
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
