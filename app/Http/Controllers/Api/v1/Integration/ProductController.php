<?php

namespace App\Http\Controllers\Api\v1\Integration;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ProductResource;
use App\Http\Traits\ApiResponse;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Product management for external platforms authenticated via API key
 * (see VerifyApiKey middleware) — create/update products, prices, stock.
 */
class ProductController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = Product::with('category')->latest();

        if ($request->filled('category_id')) {
            $query->where('store_category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->paginate(20);

        return $this->success(ProductResource::collection($products)->response()->getData(true));
    }

    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);

        return $this->success(new ProductResource($product));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'store_category_id' => 'required|exists:store_categories,id',
            'name'               => 'required|string|max:255',
            'description'        => 'nullable|string',
            'price'              => 'required|numeric|min:0',
            'sale_price'         => 'nullable|numeric|min:0',
            'stock'              => 'required|integer|min:0',
            'images'             => 'nullable|array',
            'images.*'           => 'image|max:2048',
            'is_active'          => 'boolean',
        ]);

        if ($request->hasFile('images')) {
            $data['images'] = array_map(
                fn ($image) => $image->store('store/products', 'public'),
                $request->file('images')
            );
        }

        $data['is_active'] = $request->boolean('is_active', true);

        $product = Product::create($data);

        return $this->success(new ProductResource($product->load('category')), 'تم إضافة المنتج', 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $data = $request->validate([
            'store_category_id' => 'sometimes|exists:store_categories,id',
            'name'               => 'sometimes|string|max:255',
            'description'        => 'sometimes|nullable|string',
            'price'              => 'sometimes|numeric|min:0',
            'sale_price'         => 'sometimes|nullable|numeric|min:0',
            'stock'              => 'sometimes|integer|min:0',
            'images'             => 'nullable|array',
            'images.*'           => 'image|max:2048',
            'is_active'          => 'boolean',
        ]);

        if ($request->hasFile('images')) {
            foreach ($product->images ?? [] as $oldImage) {
                Storage::disk('public')->delete($oldImage);
            }

            $data['images'] = array_map(
                fn ($image) => $image->store('store/products', 'public'),
                $request->file('images')
            );
        }

        if ($request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }

        $product->update($data);

        return $this->success(new ProductResource($product->fresh('category')), 'تم تعديل المنتج');
    }
}
