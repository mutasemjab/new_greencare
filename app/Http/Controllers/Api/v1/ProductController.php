<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ProductResource;
use App\Http\Traits\ApiResponse;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = Product::where('is_active', true)->with('category');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('store_category_id', $request->category_id);
        }

        $products = $query->paginate(20);

        return $this->success(ProductResource::collection($products)->response()->getData(true));
    }

    public function show($id)
    {
        $product = Product::where('is_active', true)
            ->with('category')
            ->findOrFail($id);

        return $this->success(new ProductResource($product));
    }
}
