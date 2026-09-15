<?php

namespace App\Http\Controllers\Api\v1\Integration;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\UserResource;
use App\Http\Traits\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Full-admin user management for external platforms authenticated via API
 * key (see VerifyApiKey middleware).
 */
class UserController extends Controller
{
    use ApiResponse;

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

        $users = $query->paginate(20);

        return $this->success(UserResource::collection($users)->response()->getData(true));
    }

    public function show($id)
    {
        $user = User::findOrFail($id);

        return $this->success(new UserResource($user));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'phone'              => 'nullable|string|max:20|unique:users',
            'email'              => 'nullable|email|unique:users',
            'role'               => 'required|in:doctor,nurse,super_nurse,university_manager,patient,patient_family',
            'date_of_birth'      => 'nullable|date',
            'gender'             => 'nullable|in:male,female',
            'related_patient_id' => 'nullable|exists:users,id',
            'is_active'          => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $user = User::create($data);

        return $this->success(new UserResource($user), 'تم إضافة المستخدم', 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'name'               => 'sometimes|string|max:255',
            'phone'              => 'nullable|string|max:20|unique:users,phone,' . $user->id,
            'email'              => 'nullable|email|unique:users,email,' . $user->id,
            'role'               => 'sometimes|in:doctor,nurse,super_nurse,university_manager,patient,patient_family',
            'date_of_birth'      => 'nullable|date',
            'gender'             => 'nullable|in:male,female',
            'related_patient_id' => 'nullable|exists:users,id',
            'is_active'          => 'boolean',
        ]);

        if ($request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }

        $user->update($data);

        return $this->success(new UserResource($user->fresh()), 'تم تعديل المستخدم');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return $this->success(null, 'تم حذف المستخدم');
    }
}
