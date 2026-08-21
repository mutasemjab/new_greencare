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
            $query->whereHas('patient', fn ($q) => $q->where('name', 'like', "%{$request->search}%"));
        }

        $visitForms = $query->paginate(20)->withQueryString();

        return view('admin.sihati.visit-forms.index', compact('visitForms'));
    }

    public function show(VisitForm $visitForm)
    {
        $visitForm->load(['patient', 'submittedBy', 'answers', 'attachments']);

        return view('admin.sihati.visit-forms.show', compact('visitForm'));
    }
}
