<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\VisitFormResource;
use App\Http\Traits\ApiResponse;
use App\Models\VisitForm;
use App\Models\VisitFormAnswer;
use App\Models\VisitFormAttachment;
use App\Models\VisitFormField;
use Illuminate\Http\Request;

class VisitFormController extends Controller
{
    use ApiResponse;

    /**
     * GET /sihati/visit-form-schema — the admin-defined dynamic form.
     */
    public function schema()
    {
        $fields = VisitFormField::active()->orderBy('sort_order')->get();

        return $this->success($fields->map(fn (VisitFormField $field) => [
            'id'         => $field->id,
            'question'   => $field->question,
            'field_type' => $field->field_type,
            'options'    => $field->options ? array_values($field->options) : [],
        ])->values());
    }

    /**
     * POST /sihati/visit-forms — super-nurse picks a patient and fills the
     * dynamic form. No room, no Firestore involvement — pure REST.
     */
    public function store(Request $request)
    {
        $user = $request->user('user-api');

        if ($user->role !== 'super_nurse') {
            abort(403, 'فقط الممرض المسؤول يمكنه تعبئة نموذج الزيارة الطبية');
        }

        $request->validate([
            'patient_id'     => 'required|exists:users,id',
            'attachments'    => 'sometimes|array',
            'attachments.*'  => 'file|mimes:png,jpg,jpeg,pdf|max:10240',
        ]);

        $fields = VisitFormField::active()->orderBy('sort_order')->get();
        $answers = $request->input('answers', []);

        foreach ($fields as $field) {
            $raw = $answers[$field->id] ?? null;

            if ($field->field_type === 'checklist') {
                if (empty($raw) && !is_array($raw)) {
                    return $this->error('الحقل "' . $field->question . '" مطلوب', null, 422);
                }
            } elseif ($raw === null || $raw === '') {
                return $this->error('الحقل "' . $field->question . '" مطلوب', null, 422);
            }

            if (in_array($field->field_type, ['choice', 'checklist']) && $field->options) {
                $selected = is_array($raw) ? $raw : [$raw];
                $invalid = array_diff($selected, $field->options);

                if (!empty($invalid)) {
                    return $this->error('قيمة غير صالحة للحقل "' . $field->question . '"', null, 422);
                }
            }
        }

        $visitForm = VisitForm::create([
            'patient_id'   => $request->patient_id,
            'submitted_by' => $user->id,
        ]);

        foreach ($fields as $field) {
            $raw = $answers[$field->id] ?? null;

            $answerData = [
                'visit_form_id'        => $visitForm->id,
                'visit_form_field_id'  => $field->id,
                'field_question'       => $field->question,
                'field_type'           => $field->field_type,
                'sort_order'           => $field->sort_order,
            ];

            if ($field->field_type === 'checklist') {
                $answerData['answer_json'] = is_array($raw) ? array_values($raw) : [];
            } else {
                $answerData['answer_text'] = is_array($raw) ? null : $raw;
            }

            VisitFormAnswer::create($answerData);
        }

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                VisitFormAttachment::create([
                    'visit_form_id' => $visitForm->id,
                    'file_path'     => $file->store('visit-forms/attachments', 'public'),
                ]);
            }
        }

        $visitForm->load('patient', 'answers', 'attachments');

        return $this->success(new VisitFormResource($visitForm), 'تم إرسال نموذج الزيارة', 201);
    }

    /**
     * GET /sihati/visit-forms/{id}
     */
    public function show(Request $request, int $id)
    {
        $user = $request->user('user-api');

        $visitForm = VisitForm::where(function ($q) use ($user) {
            $q->where('submitted_by', $user->id)->orWhere('patient_id', $user->id);
        })
            ->with('patient', 'answers', 'attachments')
            ->findOrFail($id);

        return $this->success(new VisitFormResource($visitForm));
    }
}
