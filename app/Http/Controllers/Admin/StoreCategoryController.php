<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoreCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = StoreCategory::with('parent')
            ->orderByRaw('ISNULL(parent_id), parent_id, sort_order');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        $categories = $query->paginate(20)->withQueryString();

        return view('admin.store.categories.index', compact('categories'));
    }

    public function create()
    {
        $parents = StoreCategory::whereNull('parent_id')->active()->get();

        return view('admin.store.categories.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'parent_id'  => 'nullable|exists:store_categories,id',
            'image'      => 'nullable|image|max:2048',
            'sort_order' => 'integer|min:0',
            'is_active'  => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('store/categories', 'public');
        }

        $data['is_active'] = $request->boolean('is_active', true);

        StoreCategory::create($data);

        return redirect()->route('admin.store.categories.index')
            ->with('success', 'تم إضافة التصنيف بنجاح');
    }

    public function edit(StoreCategory $storeCategory)
    {
        $parents = StoreCategory::whereNull('parent_id')
            ->where('id', '!=', $storeCategory->id)
            ->get();

        return view('admin.store.categories.edit', compact('storeCategory', 'parents'));
    }

    public function update(Request $request, StoreCategory $storeCategory)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'parent_id'  => 'nullable|exists:store_categories,id',
            'image'      => 'nullable|image|max:2048',
            'sort_order' => 'integer|min:0',
            'is_active'  => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($storeCategory->image);
            $data['image'] = $request->file('image')->store('store/categories', 'public');
        }

        $data['is_active'] = $request->boolean('is_active', false);

        $storeCategory->update($data);

        return redirect()->route('admin.store.categories.index')
            ->with('success', 'تم تعديل التصنيف بنجاح');
    }

    public function destroy(StoreCategory $storeCategory)
    {
        Storage::disk('public')->delete($storeCategory->image);
        $storeCategory->delete();

        return redirect()->route('admin.store.categories.index')
            ->with('success', 'تم حذف التصنيف');
    }
}
