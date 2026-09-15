<?php

namespace App\Http\Controllers\Api\v1\Integration;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\BannerResource;
use App\Http\Traits\ApiResponse;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Full-admin banner management for external platforms authenticated via
 * API key (see VerifyApiKey middleware).
 */
class BannerController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = Banner::query();

        if ($request->filled('section')) {
            $query->where('section', $request->section);
        }

        $banners = $query->orderBy('sort_order')->get();

        return $this->success(BannerResource::collection($banners));
    }

    public function show($id)
    {
        $banner = Banner::findOrFail($id);

        return $this->success(new BannerResource($banner));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'      => 'nullable|string|max:255',
            'image'      => 'required|image|max:2048',
            'url'        => 'nullable|url',
            'section'    => 'required|in:home,store',
            'sort_order' => 'integer|min:0',
            'is_active'  => 'boolean',
        ]);

        $data['image']     = $request->file('image')->store('banners', 'public');
        $data['is_active'] = $request->boolean('is_active', true);

        $banner = Banner::create($data);

        return $this->success(new BannerResource($banner), 'تم إضافة البنر', 201);
    }

    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $data = $request->validate([
            'title'      => 'nullable|string|max:255',
            'image'      => 'nullable|image|max:2048',
            'url'        => 'nullable|url',
            'section'    => 'sometimes|in:home,store',
            'sort_order' => 'integer|min:0',
            'is_active'  => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }
            $data['image'] = $request->file('image')->store('banners', 'public');
        }

        if ($request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }

        $banner->update($data);

        return $this->success(new BannerResource($banner->fresh()), 'تم تعديل البنر');
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);

        if ($banner->image) {
            Storage::disk('public')->delete($banner->image);
        }
        $banner->delete();

        return $this->success(null, 'تم حذف البنر');
    }
}
