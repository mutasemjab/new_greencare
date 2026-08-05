<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReportTemplate;
use App\Models\ReportTemplateField;
use Illuminate\Http\Request;

class ReportTemplateController extends Controller
{
    public function index(Request $request)
    {
        $query = ReportTemplate::withCount('fields')->latest();

        if ($request->filled('type')) {
            $query->where('template_type', $request->type);
        }

        $templates = $query->paginate(20)->withQueryString();

        return view('admin.sihati.templates.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.sihati.templates.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'template_type' => 'required|in:registration,nurse,doctor',
            'description'   => 'nullable|string',
            'is_active'     => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        ReportTemplate::create($data);

        return redirect()->route('admin.sihati.templates.index')
            ->with('success', 'تم إنشاء القالب بنجاح');
    }

    public function show(ReportTemplate $template)
    {
        $template->load(['fields' => fn($q) => $q->orderBy('sort_order')]);

        return view('admin.sihati.templates.show', compact('template'));
    }

    public function edit(ReportTemplate $template)
    {
        return view('admin.sihati.templates.edit', compact('template'));
    }

    public function update(Request $request, ReportTemplate $template)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'template_type' => 'required|in:registration,nurse,doctor',
            'description'   => 'nullable|string',
            'is_active'     => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', false);

        $template->update($data);

        return redirect()->route('admin.sihati.templates.index')
            ->with('success', 'تم تعديل القالب بنجاح');
    }

    public function destroy(ReportTemplate $template)
    {
        $template->delete();

        return redirect()->route('admin.sihati.templates.index')
            ->with('success', 'تم حذف القالب');
    }

    // ── Fields ────────────────────────────────────────────────────────────

    public function storeField(Request $request, ReportTemplate $template)
    {
        $data = $request->validate([
            'question'    => 'required|string|max:500',
            'answer_type' => 'required|in:text,number,yes_no,image',
            'is_required' => 'boolean',
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        $data['is_required'] = $request->boolean('is_required', false);
        $data['sort_order']  = $data['sort_order'] ?? ($template->fields()->max('sort_order') + 1);

        $template->fields()->create($data);

        return redirect()->route('admin.sihati.templates.show', $template)
            ->with('success', 'تم إضافة الحقل');
    }

    public function updateField(Request $request, ReportTemplateField $field)
    {
        $data = $request->validate([
            'question'    => 'required|string|max:500',
            'answer_type' => 'required|in:text,number,yes_no,image',
            'is_required' => 'boolean',
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        $data['is_required'] = $request->boolean('is_required', false);

        $field->update($data);

        return redirect()->route('admin.sihati.templates.show', $field->report_template_id)
            ->with('success', 'تم تعديل الحقل');
    }

    public function destroyField(ReportTemplateField $field)
    {
        $templateId = $field->report_template_id;
        $field->delete();

        return redirect()->route('admin.sihati.templates.show', $templateId)
            ->with('success', 'تم حذف الحقل');
    }
}
