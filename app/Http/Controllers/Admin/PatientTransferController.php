<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PatientTransfer;
use Illuminate\Http\Request;

class PatientTransferController extends Controller
{
    public function index(Request $request)
    {
        $query = PatientTransfer::with(['user', 'fromZone', 'toZone'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('phone', 'like', "%{$request->search}%"));
        }

        $transfers = $query->paginate(20)->withQueryString();

        return view('admin.transfers.index', compact('transfers'));
    }

    public function show(PatientTransfer $transfer)
    {
        $transfer->load(['user', 'fromZone', 'toZone']);

        return view('admin.transfers.show', compact('transfer'));
    }

    public function updateStatus(Request $request, PatientTransfer $transfer)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled',
        ]);

        $transfer->update(['status' => $request->status]);

        return back()->with('success', 'تم تحديث الحالة بنجاح');
    }
}
