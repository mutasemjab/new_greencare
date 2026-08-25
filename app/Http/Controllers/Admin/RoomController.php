<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChronicDisease;
use App\Models\Diagnosis;
use App\Models\ReportTemplate;
use App\Models\Room;
use App\Models\RoomAttachment;
use App\Models\RoomMember;
use App\Models\RoomTemplateAssignment;
use App\Models\User;
use App\Services\FirebaseService;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function __construct(private FirebaseService $firebase)
    {
    }

    private function intakeRules(): array
    {
        return [
            'age'                   => 'nullable|integer|min:0|max:150',
            'gender'                => 'nullable|in:male,female',
            'weight'                => 'nullable|numeric|min:0',
            'has_allergies'         => 'nullable|boolean',
            'allergy_details'       => 'nullable|string',
            'marital_status'        => 'nullable|in:single,married,divorced,widowed',
            'functional_status'     => 'nullable|in:independent,partially_dependent,fully_dependent',
            'race'                  => 'nullable|in:white,black',
            'education_level'       => 'nullable|string|max:255',
            'blood_group'           => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'diagnosis_ids'         => 'sometimes|array',
            'diagnosis_ids.*'       => 'exists:diagnoses,id',
            'chronic_disease_ids'   => 'sometimes|array',
            'chronic_disease_ids.*' => 'exists:chronic_diseases,id',
            'attachments'           => 'sometimes|array',
            'attachments.*'         => 'file|mimes:png,jpg,jpeg,pdf|max:10240',
        ];
    }

    public function create()
    {
        $patients               = User::where('role', 'patient')->get();
        $superNurses            = User::where('role', 'super_nurse')->get();
        $registrationTemplates  = ReportTemplate::active()->where('template_type', 'registration')->get();
        $diagnoses              = Diagnosis::active()->get();
        $chronicDiseases        = ChronicDisease::active()->get();

        return view('admin.sihati.rooms.create', compact(
            'patients', 'superNurses', 'registrationTemplates', 'diagnoses', 'chronicDiseases'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate(array_merge([
            'patient_id'                => 'required|exists:users,id',
            'created_by'                => 'required|exists:users,id',
            'name'                      => 'required|string|max:255',
            'description'               => 'nullable|string',
            'address'                   => 'nullable|string|max:255',
            'discount_value'            => 'nullable|numeric|min:0|max:100',
            'registration_template_id'  => 'nullable|exists:report_templates,id',
        ], $this->intakeRules()));

        $data['is_active'] = true;
        $data['has_allergies'] = $request->boolean('has_allergies');

        $room = Room::create($data);

        if ($request->filled('diagnosis_ids')) {
            $room->diagnoses()->sync($data['diagnosis_ids']);
        }

        if ($request->filled('chronic_disease_ids')) {
            $room->chronicDiseases()->sync($data['chronic_disease_ids']);
        }

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                RoomAttachment::create([
                    'room_id'       => $room->id,
                    'uploaded_by'   => null,
                    'file_path'     => $file->store('rooms/attachments', 'public'),
                    'original_name' => $file->getClientOriginalName(),
                ]);
            }
        }

        RoomMember::firstOrCreate(
            ['room_id' => $room->id, 'user_id' => $room->patient_id],
            ['role' => 'patient']
        );

        $firebaseRoomId = $this->firebase->createRoomDocument($room);
        $room->update(['firebase_room_id' => $firebaseRoomId]);
        $this->firebase->syncRoomMembers($room);

        $message = "تم إنشاء الغرفة بنجاح — كود المريض: {$room->patient_code}";

        if (! $firebaseRoomId) {
            $message .= ' — تنبيه: تعذّرت مزامنة الغرفة مع Firestore، الدردشة الحية لن تعمل حتى تتم المزامنة. راجع سجلات النظام.';
        }

        return redirect()->route('admin.sihati.rooms.show', $room)
            ->with('success', $message);
    }

    public function index(Request $request)
    {
        $query = Room::with(['patient', 'createdBy'])->withCount('members')->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhereHas('patient', fn($sq) => $sq->where('name', 'like', "%{$request->search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $rooms = $query->paginate(20)->withQueryString();

        return view('admin.sihati.rooms.index', compact('rooms'));
    }

    public function show(Room $room)
    {
        $room->load(['patient', 'createdBy', 'registrationTemplate', 'diagnoses', 'chronicDiseases', 'attachments']);

        $members = $room->members()->with('user')->get();

        $activeAssignments = RoomTemplateAssignment::where('room_id', $room->id)
            ->whereNull('unassigned_at')
            ->with('template')
            ->get();

        $assignmentHistory = RoomTemplateAssignment::where('room_id', $room->id)
            ->whereNotNull('unassigned_at')
            ->with(['template', 'assignedBy'])
            ->latest('assigned_at')
            ->take(10)
            ->get();

        $registrationReport = $room->reports()
            ->where('report_type', 'registration')
            ->with(['answers', 'submittedBy'])
            ->first();

        $reports = $room->reports()
            ->whereIn('report_type', ['nurse', 'doctor'])
            ->with('submittedBy')
            ->latest('submitted_at')
            ->take(20)
            ->get();

        $doctorOrders = $room->doctorOrders()
            ->with(['doctor', 'replies.nurse'])
            ->take(15)
            ->get();

        $medications = $room->medications()->with('addedBy')->get();

        $availableTemplates = ReportTemplate::active()
            ->whereIn('template_type', ['nurse', 'doctor'])
            ->get();

        return view('admin.sihati.rooms.show', compact(
            'room',
            'members',
            'activeAssignments',
            'assignmentHistory',
            'registrationReport',
            'reports',
            'doctorOrders',
            'medications',
            'availableTemplates',
        ));
    }

    public function edit(Room $room)
    {
        $patients               = User::where('role', 'patient')->get();
        $superNurses            = User::where('role', 'super_nurse')->get();
        $registrationTemplates  = ReportTemplate::active()->where('template_type', 'registration')->get();
        $diagnoses              = Diagnosis::active()->get();
        $chronicDiseases        = ChronicDisease::active()->get();

        $room->load('diagnoses', 'chronicDiseases', 'attachments');

        return view('admin.sihati.rooms.edit', compact(
            'room', 'patients', 'superNurses', 'registrationTemplates', 'diagnoses', 'chronicDiseases'
        ));
    }

    public function update(Request $request, Room $room)
    {
        $data = $request->validate(array_merge([
            'patient_id'                => 'required|exists:users,id',
            'created_by'                => 'required|exists:users,id',
            'name'                      => 'required|string|max:255',
            'description'               => 'nullable|string',
            'address'                   => 'nullable|string|max:255',
            'discount_value'            => 'nullable|numeric|min:0|max:100',
            'registration_template_id'  => 'nullable|exists:report_templates,id',
        ], $this->intakeRules()));

        $data['has_allergies'] = $request->boolean('has_allergies');

        $room->update($data);

        $room->diagnoses()->sync($data['diagnosis_ids'] ?? []);
        $room->chronicDiseases()->sync($data['chronic_disease_ids'] ?? []);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                RoomAttachment::create([
                    'room_id'       => $room->id,
                    'uploaded_by'   => null,
                    'file_path'     => $file->store('rooms/attachments', 'public'),
                    'original_name' => $file->getClientOriginalName(),
                ]);
            }
        }

        // Keep the patient's membership row in sync if the linked patient changed
        RoomMember::where('room_id', $room->id)
            ->where('role', 'patient')
            ->where('user_id', '!=', $room->patient_id)
            ->delete();

        RoomMember::firstOrCreate(
            ['room_id' => $room->id, 'user_id' => $room->patient_id],
            ['role' => 'patient']
        );

        // Self-heal a room that never got its Firestore doc created (e.g. Firebase
        // was down at creation time) — retry on every edit until it succeeds.
        if (! $room->firebase_room_id) {
            $room->update(['firebase_room_id' => $this->firebase->createRoomDocument($room)]);
        }

        $this->firebase->syncRoomMembers($room);

        return redirect()->route('admin.sihati.rooms.show', $room)
            ->with('success', 'تم تعديل بيانات الغرفة بنجاح');
    }

    public function destroy(Room $room)
    {
        $hasActivity = $room->reports()->exists()
            || $room->medications()->exists()
            || $room->doctorOrders()->exists();

        if ($hasActivity) {
            return back()->withErrors([
                'delete' => 'لا يمكن حذف هذه الغرفة لوجود تقارير أو أدوية أو أوامر طبيب مرتبطة بها. يمكنك تعطيلها بدلاً من ذلك.',
            ]);
        }

        $this->firebase->deleteRoomDocument($room);

        $room->delete();

        return redirect()->route('admin.sihati.rooms.index')
            ->with('success', 'تم حذف الغرفة بنجاح');
    }

    public function toggleActive(Room $room)
    {
        $room->update(['is_active' => ! $room->is_active]);

        $label = $room->is_active ? 'تفعيل' : 'تعطيل';

        return back()->with('success', "تم {$label} الغرفة");
    }

    public function addMember(Request $request, Room $room)
    {
        $request->validate([
            'phone' => 'required|string',
            'role'  => 'required|in:doctor,nurse,patient_family,super_nurse',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (! $user) {
            return back()->withErrors(['phone' => 'لم يتم العثور على مستخدم بهذا الرقم'])->withInput();
        }

        if ($room->members()->where('user_id', $user->id)->exists()) {
            return back()->withErrors(['phone' => 'المستخدم عضو في هذه الغرفة مسبقاً'])->withInput();
        }

        RoomMember::create([
            'room_id' => $room->id,
            'user_id' => $user->id,
            'role'    => $request->role,
        ]);

        $this->firebase->syncRoomMembers($room);

        return back()->with('success', "تم إضافة {$user->name} للغرفة");
    }

    public function removeMember(Room $room, RoomMember $member)
    {
        abort_if($member->room_id !== $room->id, 403);

        $member->delete();

        $this->firebase->syncRoomMembers($room);

        return back()->with('success', 'تم إزالة العضو من الغرفة');
    }

    public function assignTemplate(Request $request, Room $room)
    {
        $request->validate([
            'report_template_id' => 'required|exists:report_templates,id',
        ]);

        $template = ReportTemplate::findOrFail($request->report_template_id);

        if ($template->template_type === 'registration') {
            return back()->withErrors(['report_template_id' => 'لا يمكن تعيين قالب التسجيل من هنا']);
        }

        // Archive any existing active assignment of the same type
        RoomTemplateAssignment::where('room_id', $room->id)
            ->whereNull('unassigned_at')
            ->whereHas('template', fn($q) => $q->where('template_type', $template->template_type))
            ->update(['unassigned_at' => now()]);

        RoomTemplateAssignment::create([
            'room_id'            => $room->id,
            'report_template_id' => $template->id,
            'assigned_by'        => auth('admin')->id(),
            'assigned_at'        => now(),
        ]);

        return back()->with('success', "تم تعيين القالب \"{$template->name}\" للغرفة");
    }
}
