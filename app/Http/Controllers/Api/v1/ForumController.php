<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ForumCategoryResource;
use App\Http\Resources\Api\ForumPostResource;
use App\Http\Resources\Api\ForumReplyResource;
use App\Http\Resources\Api\ForumSubCategoryResource;
use App\Http\Traits\ApiResponse;
use App\Models\ForumCategory;
use App\Models\ForumPost;
use App\Models\ForumReply;
use App\Models\ForumSubCategory;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    use ApiResponse;

    public function categories()
    {
        $categories = ForumCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return $this->success(ForumCategoryResource::collection($categories));
    }

    public function subCategories(Request $request)
    {
        $query = ForumSubCategory::where('is_active', true)
            ->with('category')
            ->orderBy('sort_order');

        if ($request->filled('category_id')) {
            $query->where('forum_category_id', $request->category_id);
        }

        return $this->success(ForumSubCategoryResource::collection($query->get()));
    }

    public function posts(Request $request)
    {
        $query = ForumPost::where('is_active', true)
            ->with(['user' => fn ($q) => $q->select('id', 'name', 'phone')]);

        if ($request->filled('sub_category_id')) {
            $query->where('forum_sub_category_id', $request->sub_category_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $posts = $query->orderByDesc('is_pinned')
            ->latest()
            ->paginate(15);

        return $this->success(ForumPostResource::collection($posts)->response()->getData(true));
    }

    public function showPost(int $id)
    {
        $post = ForumPost::where('is_active', true)
            ->with([
                'user'    => fn ($q) => $q->select('id', 'name', 'phone'),
                'replies' => fn ($q) => $q->where('is_active', true)
                    ->with(['user' => fn ($q2) => $q2->select('id', 'name')]),
            ])
            ->findOrFail($id);

        return $this->success(new ForumPostResource($post));
    }

    public function storePost(Request $request)
    {
        $request->validate([
            'sub_category_id' => 'required|exists:forum_sub_categories,id',
            'title'           => 'required|string|max:255',
            'body'            => 'required|string',
            'type'            => 'required|in:experience,question',
        ]);

        $post = ForumPost::create([
            'user_id'               => $request->user('user-api')->id,
            'forum_sub_category_id' => $request->sub_category_id,
            'title'                 => $request->title,
            'content'               => $request->body,
            'type'                  => $request->type,
            'is_active'             => true,
            'is_pinned'             => false,
            'replies_count'         => 0,
        ]);

        $post->load(['user' => fn ($q) => $q->select('id', 'name', 'phone')]);

        return $this->success(new ForumPostResource($post), 'تم نشر المنشور', 201);
    }

    public function destroyPost(Request $request, int $id)
    {
        $post = ForumPost::findOrFail($id);

        if ($post->user_id !== $request->user('user-api')->id) {
            abort(403, 'غير مصرح لك بحذف هذا المنشور');
        }

        $post->delete();

        return $this->success(null, 'تم حذف المنشور');
    }

    public function storeReply(Request $request, int $postId)
    {
        $request->validate([
            'body' => 'required|string',
        ]);

        $post = ForumPost::where('is_active', true)->findOrFail($postId);

        $reply = ForumReply::create([
            'forum_post_id' => $post->id,
            'user_id'       => $request->user('user-api')->id,
            'content'       => $request->body,
            'is_active'     => true,
        ]);

        $reply->load(['user' => fn ($q) => $q->select('id', 'name')]);

        return $this->success(new ForumReplyResource($reply), 'تم إضافة الرد', 201);
    }

    public function destroyReply(Request $request, int $id)
    {
        $reply = ForumReply::findOrFail($id);

        if ($reply->user_id !== $request->user('user-api')->id) {
            abort(403, 'غير مصرح لك بحذف هذا الرد');
        }

        $reply->delete();

        return $this->success(null, 'تم حذف الرد');
    }
}
