<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NutritionRequest;
use Illuminate\Http\Request;

class NutritionController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->can('nutrition-table')) {
            abort(403);
        }

        $query = NutritionRequest::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('phone', 'like', "%{$request->search}%"));
        }

        $requests = $query->paginate(20)->withQueryString();

        return view('admin.nutrition.index', compact('requests'));
    }

    public function show(NutritionRequest $nutrition)
    {
        if (!auth()->user()->can('nutrition-table')) {
            abort(403);
        }

        $nutrition->load('user');

        return view('admin.nutrition.show', compact('nutrition'));
    }

    public function updateStatus(Request $request, NutritionRequest $nutrition)
    {
        if (!auth()->user()->can('nutrition-edit')) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled',
        ]);

        $nutrition->update(['status' => $request->status]);

        return back()->with('success', 'تم تحديث الحالة بنجاح');
    }

    public function destroy(NutritionRequest $nutrition)
    {
        if (!auth()->user()->can('nutrition-delete')) {
            abort(403);
        }

        $nutrition->delete();

        return redirect()->route('admin.nutrition.index')
            ->with('success', 'تم حذف الطلب');
    }
}
