<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $query = Complaint::with(['room', 'patient', 'submittedBy'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->whereHas('patient', fn ($q) => $q->where('name', 'like', "%{$request->search}%"));
        }

        $complaints = $query->paginate(20)->withQueryString();

        return view('admin.sihati.complaints.index', compact('complaints'));
    }

    public function show(Complaint $complaint)
    {
        $complaint->load(['room', 'patient', 'submittedBy']);

        return view('admin.sihati.complaints.show', compact('complaint'));
    }

    public function markReviewed(Complaint $complaint)
    {
        $complaint->update(['status' => 'reviewed']);

        return back()->with('success', 'تم وضع علامة "تمت المراجعة" على الشكوى');
    }
}
