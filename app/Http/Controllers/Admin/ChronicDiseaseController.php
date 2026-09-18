<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChronicDisease;
use Illuminate\Http\Request;

class ChronicDiseaseController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->can('chronic-disease-table')) {
            abort(403);
        }

        $query = ChronicDisease::latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $chronicDiseases = $query->paginate(20)->withQueryString();

        return view('admin.sihati.chronic-diseases.index', compact('chronicDiseases'));
    }

    public function create()
    {
        if (!auth()->user()->can('chronic-disease-add')) {
            abort(403);
        }

        return view('admin.sihati.chronic-diseases.create');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('chronic-disease-add')) {
            abort(403);
        }

        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        ChronicDisease::create($data);

        return redirect()->route('admin.sihati.chronic-diseases.index')
            ->with('success', 'تم إضافة المرض المزمن بنجاح');
    }

    public function edit(ChronicDisease $chronicDisease)
    {
        if (!auth()->user()->can('chronic-disease-edit')) {
            abort(403);
        }

        return view('admin.sihati.chronic-diseases.edit', compact('chronicDisease'));
    }

    public function update(Request $request, ChronicDisease $chronicDisease)
    {
        if (!auth()->user()->can('chronic-disease-edit')) {
            abort(403);
        }

        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', false);

        $chronicDisease->update($data);

        return redirect()->route('admin.sihati.chronic-diseases.index')
            ->with('success', 'تم تعديل المرض المزمن بنجاح');
    }

    public function destroy(ChronicDisease $chronicDisease)
    {
        if (!auth()->user()->can('chronic-disease-delete')) {
            abort(403);
        }

        $chronicDisease->delete();

        return redirect()->route('admin.sihati.chronic-diseases.index')
            ->with('success', 'تم حذف المرض المزمن');
    }
}
