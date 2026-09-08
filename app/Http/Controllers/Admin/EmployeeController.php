<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->can('employee-table')) {
            abort(403);
        }

        $query = Admin::where('is_super', false);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('username', 'LIKE', "%{$search}%");
            });
        }

        $data = $query->latest()->paginate(15)->withQueryString();

        return view('admin.employee.index', compact('data'));
    }

    public function create()
    {
        if (!auth()->user()->can('employee-add')) {
            abort(403);
        }

        $roles = Role::where('guard_name', 'admin')->get();
        return view('admin.employee.create', compact('roles'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('employee-add')) {
            abort(403);
        }

        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:admins,email',
            'username' => 'required|string|max:100|unique:admins,username',
            'password' => 'required|string|min:6',
            'roles'    => 'required|array',
        ], [
            'name.required'     => 'الاسم مطلوب',
            'email.required'    => 'البريد الإلكتروني مطلوب',
            'email.unique'      => 'البريد الإلكتروني مستخدم مسبقاً',
            'username.required' => 'اسم المستخدم مطلوب',
            'username.unique'   => 'اسم المستخدم مستخدم مسبقاً',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min'      => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل',
            'roles.required'    => 'يجب اختيار دور واحد على الأقل',
        ]);

        DB::beginTransaction();
        try {
            $admin = Admin::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
            ]);

            $admin->syncRoles($request->roles);

            DB::commit();
            return redirect()->route('admin.employee.index')
                ->with('success', 'تم إضافة الموظف بنجاح');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Employee store: ' . $e->getMessage());
            return redirect()->back()->withInput()
                ->with('error', 'حدث خطأ أثناء الحفظ');
        }
    }

    public function edit($id)
    {
        if (!auth()->user()->can('employee-edit')) {
            abort(403);
        }

        $admin     = Admin::findOrFail($id);
        $roles     = Role::where('guard_name', 'admin')->get();
        $adminRole = $admin->roles->pluck('id')->all();

        return view('admin.employee.edit', compact('admin', 'roles', 'adminRole'));
    }

    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('employee-edit')) {
            abort(403);
        }

        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:admins,email,' . $id,
            'username' => 'required|string|max:100|unique:admins,username,' . $id,
            'roles'    => 'required|array',
            'password' => 'nullable|string|min:6',
        ], [
            'name.required'     => 'الاسم مطلوب',
            'email.required'    => 'البريد الإلكتروني مطلوب',
            'email.unique'      => 'البريد الإلكتروني مستخدم مسبقاً',
            'username.required' => 'اسم المستخدم مطلوب',
            'username.unique'   => 'اسم المستخدم مستخدم مسبقاً',
            'roles.required'    => 'يجب اختيار دور واحد على الأقل',
            'password.min'      => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل',
        ]);

        DB::beginTransaction();
        try {
            $admin           = Admin::findOrFail($id);
            $admin->name     = $request->name;
            $admin->email    = $request->email;
            $admin->username = $request->username;

            if ($request->filled('password')) {
                $admin->password = Hash::make($request->password);
            }

            $admin->save();
            $admin->syncRoles($request->roles);

            DB::commit();
            return redirect()->route('admin.employee.index')
                ->with('success', 'تم تحديث بيانات الموظف بنجاح');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Employee update: ' . $e->getMessage());
            return redirect()->back()->withInput()
                ->with('error', 'حدث خطأ أثناء التحديث');
        }
    }

    public function delete(Request $request)
    {
        if (!auth()->user()->can('employee-delete')) {
            return 0;
        }

        try {
            $admin = Admin::where('id', $request->id)
                          ->where('is_super', false)
                          ->firstOrFail();
            $admin->syncRoles([]);
            $admin->delete();
            return 1;
        } catch (Exception $e) {
            Log::error('Employee delete: ' . $e->getMessage());
            return 0;
        }
    }
}
