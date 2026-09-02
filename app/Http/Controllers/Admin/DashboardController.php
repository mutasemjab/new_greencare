<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BathingRequest;
use App\Models\CareRequest;
use App\Models\DoctorBooking;
use App\Models\LabRequest;
use App\Models\NursingRequest;
use App\Models\Order;
use App\Models\PatientTransfer;
use App\Models\Room;
use App\Models\User;
use App\Models\XrayRequest;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Pending requests ─────────────────────────────────────────────────
        $pendingOrders    = Order::where('status', 'pending')->count();
        $pendingDoctors   = DoctorBooking::where('status', 'pending')->count();
        $pendingLab       = LabRequest::where('status', 'pending')->count();
        $pendingXray      = XrayRequest::where('status', 'pending')->count();
        $pendingNursing   = NursingRequest::where('status', 'pending')->count();
        $pendingCare      = CareRequest::where('status', 'pending')->count();
        $pendingBathing   = BathingRequest::where('status', 'pending')->count();
        $pendingTransfer  = PatientTransfer::where('status', 'pending')->count();

        // ── Totals ───────────────────────────────────────────────────────────
        $totalRooms    = Room::count();
        $totalPatients = User::where('role', 'patient')->count();
        $totalDoctors  = User::where('role', 'doctor')->count();
        $totalNurses   = User::whereIn('role', ['nurse', 'super_nurse'])->count();
        $totalFamilies = User::where('role', 'patient_family')->count();

        // ── Recent data ──────────────────────────────────────────────────────
        $recentOrders  = Order::with('user')->latest()->limit(6)->get();
        $recentLab     = LabRequest::with('user')->latest()->limit(6)->get();
        $recentDoctors = DoctorBooking::with(['user', 'doctor'])->latest()->limit(6)->get();

        return view('admin.dashboard', compact(
            'pendingOrders', 'pendingDoctors', 'pendingLab', 'pendingXray',
            'pendingNursing', 'pendingCare', 'pendingBathing', 'pendingTransfer',
            'totalRooms', 'totalPatients', 'totalDoctors', 'totalNurses', 'totalFamilies',
            'recentOrders', 'recentLab', 'recentDoctors'
        ));
    }
}
