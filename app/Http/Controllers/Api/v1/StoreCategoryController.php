<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ProductResource;
use App\Http\Resources\Api\StoreCategoryResource;
use App\Http\Traits\ApiResponse;
use App\Models\Product;
use App\Models\StoreCategory;
use Illuminate\Http\Request;

class StoreCategoryController extends Controller
{
    use ApiResponse;

    /**
     * List root (parent) categories with active children.
     */
    public function index()
    {
        $categories = StoreCategory::active()
            ->whereNull('parent_id')
            ->with(['children' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
            ->get();

        return $this->success(StoreCategoryResource::collection($categories));
    }

    /**
     * Products belonging to category or any of its children.
     */
    public function products(Request $request, $categoryId)
    {
        $category = StoreCategory::findOrFail($categoryId);

        // Collect this category + all child IDs
        $childIds = $category->children()->pluck('id')->toArray();
        $categoryIds = array_merge([(int) $categoryId], $childIds);

        $query = Product::whereIn('store_category_id', $categoryIds)
            ->where('is_active', true)
            ->with('category');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->paginate(20);

        return $this->success(ProductResource::collection($products)->response()->getData(true));
    }
}
