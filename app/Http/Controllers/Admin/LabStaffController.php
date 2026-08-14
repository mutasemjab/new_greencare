<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LabStaff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LabStaffController extends Controller
{
    public function index(Request $request)
    {
        $query = LabStaff::latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        $staff = $query->paginate(20)->withQueryString();

        return view('admin.lab.staff.index', compact('staff'));
    }

    public function create()
    {
        return view('admin.lab.staff.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'phone'    => 'required|string|max:20|unique:lab_staff,phone',
            'password' => 'required|string|min:6',
        ]);

        $data['password']  = Hash::make($data['password']);
        $data['is_active'] = $request->boolean('is_active', true);

        LabStaff::create($data);

        return redirect()->route('admin.lab.staff.index')
            ->with('success', 'تم إضافة حساب المختبر بنجاح');
    }

    public function edit(LabStaff $labStaffMember)
    {
        return view('admin.lab.staff.edit', ['staffMember' => $labStaffMember]);
    }

    public function update(Request $request, LabStaff $labStaffMember)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'phone'    => 'required|string|max:20|unique:lab_staff,phone,' . $labStaffMember->id,
            'password' => 'nullable|string|min:6',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['is_active'] = $request->boolean('is_active', false);

        $labStaffMember->update($data);

        return redirect()->route('admin.lab.staff.index')
            ->with('success', 'تم تعديل حساب المختبر بنجاح');
    }

    public function destroy(LabStaff $labStaffMember)
    {
        $labStaffMember->delete();

        return redirect()->route('admin.lab.staff.index')
            ->with('success', 'تم حذف حساب المختبر');
    }
}
