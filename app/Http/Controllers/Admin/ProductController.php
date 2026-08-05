<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StoreCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->latest();

        if ($request->filled('category_id')) {
            $query->where('store_category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $products   = $query->paginate(20)->withQueryString();
        $categories = StoreCategory::active()->get();

        return view('admin.store.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = StoreCategory::active()->get();

        return view('admin.store.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'store_category_id' => 'required|exists:store_categories,id',
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
            'price'             => 'required|numeric|min:0',
            'sale_price'        => 'nullable|numeric|min:0',
            'stock'             => 'required|integer|min:0',
            'images'            => 'nullable|array',
            'images.*'          => 'image|max:2048',
            'is_active'         => 'boolean',
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('store/products', 'public');
            }
        }

        $data['images']    = $imagePaths ?: null;
        $data['is_active'] = $request->boolean('is_active', true);

        Product::create($data);

        return redirect()->route('admin.store.products.index')
            ->with('success', 'تم إضافة المنتج بنجاح');
    }

    public function edit(Product $product)
    {
        $categories = StoreCategory::active()->get();

        return view('admin.store.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'store_category_id' => 'required|exists:store_categories,id',
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
            'price'             => 'required|numeric|min:0',
            'sale_price'        => 'nullable|numeric|min:0',
            'stock'             => 'required|integer|min:0',
            'images'            => 'nullable|array',
            'images.*'          => 'image|max:2048',
            'is_active'         => 'boolean',
        ]);

        $existingImages = $product->images ?? [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $existingImages[] = $image->store('store/products', 'public');
            }
        }

        // handle deleted images
        if ($request->filled('deleted_images')) {
            foreach ($request->deleted_images as $path) {
                Storage::disk('public')->delete($path);
                $existingImages = array_filter($existingImages, fn($img) => $img !== $path);
            }
        }

        $data['images']    = array_values($existingImages) ?: null;
        $data['is_active'] = $request->boolean('is_active', false);

        $product->update($data);

        return redirect()->route('admin.store.products.index')
            ->with('success', 'تم تعديل المنتج بنجاح');
    }

    public function destroy(Product $product)
    {
        foreach ($product->images ?? [] as $image) {
            Storage::disk('public')->delete($image);
        }
        $product->delete();

        return redirect()->route('admin.store.products.index')
            ->with('success', 'تم حذف المنتج');
    }
}
