<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Medication;
use Illuminate\Http\Request;

class MedicationController extends Controller
{
    public function index(Request $request)
    {
        $query = Medication::with('patient')->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('medication_name', 'like', "%{$request->search}%")
                  ->orWhereHas('patient', fn($sq) => $sq->where('name', 'like', "%{$request->search}%"));
            });
        }

        $medications = $query->paginate(25)->withQueryString();

        return view('admin.sihati.medications.index', compact('medications'));
    }

    public function show(Medication $medication)
    {
        $medication->load('patient');

        return view('admin.sihati.medications.show', compact('medication'));
    }
}
