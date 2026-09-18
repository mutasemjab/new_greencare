<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->can('complaint-table')) {
            abort(403);
        }

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
        if (!auth()->user()->can('complaint-table')) {
            abort(403);
        }

        $complaint->load(['room', 'patient', 'submittedBy']);

        return view('admin.sihati.complaints.show', compact('complaint'));
    }

    public function markReviewed(Complaint $complaint)
    {
        if (!auth()->user()->can('complaint-edit')) {
            abort(403);
        }

        $complaint->update(['status' => 'reviewed']);

        return back()->with('success', 'تم وضع علامة "تمت المراجعة" على الشكوى');
    }
}
