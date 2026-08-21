<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Diagnosis;
use Illuminate\Http\Request;

class DiagnosisController extends Controller
{
    public function index(Request $request)
    {
        $query = Diagnosis::latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $diagnoses = $query->paginate(20)->withQueryString();

        return view('admin.sihati.diagnoses.index', compact('diagnoses'));
    }

    public function create()
    {
        return view('admin.sihati.diagnoses.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        Diagnosis::create($data);

        return redirect()->route('admin.sihati.diagnoses.index')
            ->with('success', 'تم إضافة التشخيص بنجاح');
    }

    public function edit(Diagnosis $diagnosis)
    {
        return view('admin.sihati.diagnoses.edit', compact('diagnosis'));
    }

    public function update(Request $request, Diagnosis $diagnosis)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', false);

        $diagnosis->update($data);

        return redirect()->route('admin.sihati.diagnoses.index')
            ->with('success', 'تم تعديل التشخيص بنجاح');
    }

    public function destroy(Diagnosis $diagnosis)
    {
        $diagnosis->delete();

        return redirect()->route('admin.sihati.diagnoses.index')
            ->with('success', 'تم حذف التشخيص');
    }
}
