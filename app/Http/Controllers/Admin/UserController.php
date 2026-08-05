<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->latest();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('patient_code', 'like', "%{$request->search}%");
            });
        }

        $users = $query->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'phone'              => 'nullable|string|max:20|unique:users',
            'email'              => 'nullable|email|unique:users',
            'role'               => 'required|in:doctor,nurse,head_nurse,university_manager,patient,patient_family',
            'fcm_token'          => 'nullable|string',
            'date_of_birth'      => 'nullable|date',
            'gender'             => 'nullable|in:male,female',
            'related_patient_id' => 'nullable|exists:users,id',
            'is_active'          => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        User::create($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'تم إضافة المستخدم بنجاح');
    }

    public function show(User $user)
    {
        $user->load(['addresses', 'orders', 'nursingRequests', 'bathingRequests', 'careRequests', 'labRequests', 'xrayRequests']);

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $patients = User::where('role', 'patient')->where('id', '!=', $user->id)->get();

        return view('admin.users.edit', compact('user', 'patients'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'phone'              => 'nullable|string|max:20|unique:users,phone,' . $user->id,
            'email'              => 'nullable|email|unique:users,email,' . $user->id,
            'role'               => 'required|in:doctor,nurse,head_nurse,university_manager,patient,patient_family',
            'fcm_token'          => 'nullable|string',
            'date_of_birth'      => 'nullable|date',
            'gender'             => 'nullable|in:male,female',
            'related_patient_id' => 'nullable|exists:users,id',
            'is_active'          => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', false);

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'تم تعديل المستخدم بنجاح');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'تم حذف المستخدم');
    }
}
