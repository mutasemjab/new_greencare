<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $section = $request->get('section', 'home');
        $banners = Banner::where('section', $section)->orderBy('sort_order')->get();

        return view('admin.banners.index', compact('banners', 'section'));
    }

    public function create(Request $request)
    {
        $section = $request->get('section', 'home');

        return view('admin.banners.create', compact('section'));
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

        Banner::create($data);

        return redirect()->route('admin.banners.index', ['section' => $data['section']])
            ->with('success', 'تم إضافة البنر بنجاح');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $data = $request->validate([
            'title'      => 'nullable|string|max:255',
            'image'      => 'nullable|image|max:2048',
            'url'        => 'nullable|url',
            'section'    => 'required|in:home,store',
            'sort_order' => 'integer|min:0',
            'is_active'  => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }
            $data['image'] = $request->file('image')->store('banners', 'public');
        }

        $data['is_active'] = $request->boolean('is_active', false);

        $banner->update($data);

        return redirect()->route('admin.banners.index', ['section' => $banner->section])
            ->with('success', 'تم تعديل البنر بنجاح');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->image) {
            Storage::disk('public')->delete($banner->image);
        }
        $section = $banner->section;
        $banner->delete();

        return redirect()->route('admin.banners.index', ['section' => $section])
            ->with('success', 'تم حذف البنر');
    }
}
