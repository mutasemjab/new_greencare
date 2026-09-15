<?php

namespace App\Http\Controllers\Api\v1\Integration;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\StoreCategoryResource;
use App\Http\Traits\ApiResponse;
use App\Models\StoreCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Store-category management for external platforms authenticated via API
 * key (see VerifyApiKey middleware).
 */
class CategoryController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $categories = StoreCategory::with('children')->orderByRaw('ISNULL(parent_id), parent_id, sort_order')->get();

        return $this->success(StoreCategoryResource::collection($categories));
    }

    public function show($id)
    {
        $category = StoreCategory::with('children')->findOrFail($id);

        return $this->success(new StoreCategoryResource($category));
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

        $category = StoreCategory::create($data);

        return $this->success(new StoreCategoryResource($category), 'تم إضافة التصنيف', 201);
    }

    public function update(Request $request, $id)
    {
        $category = StoreCategory::findOrFail($id);

        $data = $request->validate([
            'name'       => 'sometimes|string|max:255',
            'parent_id'  => 'nullable|exists:store_categories,id',
            'image'      => 'nullable|image|max:2048',
            'sort_order' => 'integer|min:0',
            'is_active'  => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store('store/categories', 'public');
        }

        if ($request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }

        $category->update($data);

        return $this->success(new StoreCategoryResource($category->fresh()), 'تم تعديل التصنيف');
    }
}
