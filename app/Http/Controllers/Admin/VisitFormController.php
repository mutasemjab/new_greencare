<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VisitForm;
use Illuminate\Http\Request;

class VisitFormController extends Controller
{
    public function index(Request $request)
    {
        $query = VisitForm::with(['patient', 'submittedBy'])->latest();

        if ($request->filled('search')) {
            $query->where('code', 'like', "%{$request->search}%")
                ->orWhereHas('patient', fn ($q) => $q->where('name', 'like', "%{$request->search}%"));
        }

        $visitForms = $query->paginate(20)->withQueryString();

        return view('admin.sihati.visit-forms.index', compact('visitForms'));
    }

    public function show(VisitForm $visitForm)
    {
        $visitForm->load([
            'patient', 'submittedBy', 'answers', 'attachments',
            'labRequests.tests.test', 'xrayRequests.tests.test',
        ]);

        return view('admin.sihati.visit-forms.show', compact('visitForm'));
    }

    public function updateDiscount(Request $request, VisitForm $visitForm)
    {
        $request->validate([
            'discount_value' => 'nullable|numeric|min:0|max:100',
        ]);

        $visitForm->update(['discount_value' => $request->discount_value ?? 0]);

        return back()->with('success', 'تم تحديث نسبة الخصم بنجاح');
    }
}
