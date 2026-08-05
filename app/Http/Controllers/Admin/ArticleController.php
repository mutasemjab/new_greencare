<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::latest();

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        $articles = $query->paginate(20)->withQueryString();

        return view('admin.articles.index', compact('articles'));
    }

    public function create()
    {
        return view('admin.articles.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'required|string',
            'image'        => 'nullable|image|max:2048',
            'is_active'    => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('articles', 'public');
        }

        $data['is_active'] = $request->boolean('is_active', true);

        Article::create($data);

        return redirect()->route('admin.articles.index')
            ->with('success', 'تم إضافة المقال بنجاح');
    }

    public function edit(Article $article)
    {
        return view('admin.articles.edit', compact('article'));
    }

    public function update(Request $request, Article $article)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'required|string',
            'image'        => 'nullable|image|max:2048',
            'is_active'    => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($article->image);
            $data['image'] = $request->file('image')->store('articles', 'public');
        }

        $data['is_active'] = $request->boolean('is_active', false);

        $article->update($data);

        return redirect()->route('admin.articles.index')
            ->with('success', 'تم تعديل المقال بنجاح');
    }

    public function destroy(Article $article)
    {
        Storage::disk('public')->delete($article->image);
        $article->delete();

        return redirect()->route('admin.articles.index')
            ->with('success', 'تم حذف المقال');
    }
}
