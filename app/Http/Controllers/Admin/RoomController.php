<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReportTemplate;
use App\Models\Room;
use App\Models\RoomMember;
use App\Models\RoomTemplateAssignment;
use App\Models\User;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function create()
    {
        $patients          = User::where('role', 'patient')->get();
        $headNurses         = User::where('role', 'head_nurse')->get();
        $registrationTemplates = ReportTemplate::active()->where('template_type', 'registration')->get();

        return view('admin.sihati.rooms.create', compact('patients', 'headNurses', 'registrationTemplates'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id'                => 'required|exists:users,id',
            'created_by'                => 'required|exists:users,id',
            'name'                      => 'required|string|max:255',
            'description'               => 'nullable|string',
            'address'                   => 'nullable|string|max:255',
            'discount_value'            => 'nullable|numeric|min:0|max:100',
            'registration_template_id'  => 'nullable|exists:report_templates,id',
        ]);

        $data['is_active'] = true;

        $room = Room::create($data);

        return redirect()->route('admin.sihati.rooms.show', $room)
            ->with('success', "تم إنشاء الغرفة بنجاح — كود المريض: {$room->patient_code}");
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
        $room->load(['patient', 'createdBy', 'registrationTemplate']);

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
            'role'  => 'required|in:doctor,nurse,patient_family',
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

        return back()->with('success', "تم إضافة {$user->name} للغرفة");
    }

    public function removeMember(Room $room, RoomMember $member)
    {
        abort_if($member->room_id !== $room->id, 403);

        $member->delete();

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
