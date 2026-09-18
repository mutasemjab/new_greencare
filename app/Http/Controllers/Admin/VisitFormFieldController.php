<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VisitFormField;
use Illuminate\Http\Request;

class VisitFormFieldController extends Controller
{
    private function parseOptions(Request $request): ?array
    {
        if (!in_array($request->field_type, ['choice', 'checklist'])) {
            return null;
        }

        $lines = preg_split('/\r\n|\r|\n/', (string) $request->input('options', ''));
        $options = array_values(array_filter(array_map('trim', $lines), fn ($line) => $line !== ''));

        return $options ?: null;
    }

    public function index()
    {
        if (!auth()->user()->can('visit-form-field-table')) {
            abort(403);
        }

        $fields = VisitFormField::orderBy('sort_order')->paginate(20);

        return view('admin.sihati.visit-form-fields.index', compact('fields'));
    }

    public function create()
    {
        if (!auth()->user()->can('visit-form-field-add')) {
            abort(403);
        }

        return view('admin.sihati.visit-form-fields.create');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('visit-form-field-add')) {
            abort(403);
        }

        $data = $request->validate([
            'question'   => 'required|string|max:500',
            'field_type' => 'required|in:text,number,choice,checklist',
            'options'    => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active'  => 'boolean',
        ]);

        $data['options'] = $this->parseOptions($request);
        $data['sort_order'] = $data['sort_order'] ?? ((int) VisitFormField::max('sort_order') + 1);
        $data['is_active'] = $request->boolean('is_active', true);

        if (in_array($data['field_type'], ['choice', 'checklist']) && empty($data['options'])) {
            return back()->withErrors(['options' => 'الرجاء إدخال خيار واحد على الأقل'])->withInput();
        }

        VisitFormField::create($data);

        return redirect()->route('admin.sihati.visit-form-fields.index')
            ->with('success', 'تم إضافة الحقل بنجاح');
    }

    public function edit(VisitFormField $visitFormField)
    {
        if (!auth()->user()->can('visit-form-field-edit')) {
            abort(403);
        }

        return view('admin.sihati.visit-form-fields.edit', ['field' => $visitFormField]);
    }

    public function update(Request $request, VisitFormField $visitFormField)
    {
        if (!auth()->user()->can('visit-form-field-edit')) {
            abort(403);
        }

        $data = $request->validate([
            'question'   => 'required|string|max:500',
            'field_type' => 'required|in:text,number,choice,checklist',
            'options'    => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active'  => 'boolean',
        ]);

        $data['options'] = $this->parseOptions($request);
        $data['is_active'] = $request->boolean('is_active', false);

        if (in_array($data['field_type'], ['choice', 'checklist']) && empty($data['options'])) {
            return back()->withErrors(['options' => 'الرجاء إدخال خيار واحد على الأقل'])->withInput();
        }

        $visitFormField->update($data);

        return redirect()->route('admin.sihati.visit-form-fields.index')
            ->with('success', 'تم تعديل الحقل بنجاح');
    }

    public function destroy(VisitFormField $visitFormField)
    {
        if (!auth()->user()->can('visit-form-field-delete')) {
            abort(403);
        }

        $visitFormField->delete();

        return redirect()->route('admin.sihati.visit-form-fields.index')
            ->with('success', 'تم حذف الحقل');
    }
}
