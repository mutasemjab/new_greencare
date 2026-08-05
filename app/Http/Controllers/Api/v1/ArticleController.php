<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ArticleResource;
use App\Http\Traits\ApiResponse;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = Article::where('is_active', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $articles = $query->orderByDesc('published_at')->paginate(15);

        return $this->success(ArticleResource::collection($articles)->response()->getData(true));
    }

    public function show(int $id)
    {
        $article = Article::where('is_active', true)->findOrFail($id);

        return $this->success(new ArticleResource($article));
    }
}
