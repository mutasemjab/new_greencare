<?php

namespace App\Http\Controllers\Api\v1\Integration;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ArticleResource;
use App\Http\Traits\ApiResponse;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Full-admin article management for external platforms authenticated via
 * API key (see VerifyApiKey middleware).
 */
class ArticleController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = Article::latest();

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        $articles = $query->paginate(20);

        return $this->success(ArticleResource::collection($articles)->response()->getData(true));
    }

    public function show($id)
    {
        $article = Article::findOrFail($id);

        return $this->success(new ArticleResource($article));
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

        $article = Article::create($data);

        return $this->success(new ArticleResource($article), 'تم إضافة المقال', 201);
    }

    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        $data = $request->validate([
            'title'        => 'sometimes|string|max:255',
            'description'  => 'sometimes|string',
            'image'        => 'nullable|image|max:2048',
            'is_active'    => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        if ($request->hasFile('image')) {
            if ($article->image) {
                Storage::disk('public')->delete($article->image);
            }
            $data['image'] = $request->file('image')->store('articles', 'public');
        }

        if ($request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }

        $article->update($data);

        return $this->success(new ArticleResource($article->fresh()), 'تم تعديل المقال');
    }

    public function destroy($id)
    {
        $article = Article::findOrFail($id);

        if ($article->image) {
            Storage::disk('public')->delete($article->image);
        }
        $article->delete();

        return $this->success(null, 'تم حذف المقال');
    }
}
