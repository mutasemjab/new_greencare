<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumCategory;
use App\Models\ForumPost;
use App\Models\ForumReply;
use App\Models\ForumSubCategory;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    // ── Categories ────────────────────────────────────────────────────────

    public function categories()
    {
        $categories = ForumCategory::withCount('subCategories')->orderBy('sort_order')->get();

        return view('admin.forum.categories.index', compact('categories'));
    }

    public function createCategory()
    {
        return view('admin.forum.categories.create');
    }

    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'icon'       => 'nullable|image|max:1024',
            'sort_order' => 'integer|min:0',
            'is_active'  => 'boolean',
        ]);

        if ($request->hasFile('icon')) {
            $data['icon'] = $request->file('icon')->store('forum/icons', 'public');
        }

        $data['is_active'] = $request->boolean('is_active', true);

        ForumCategory::create($data);

        return redirect()->route('admin.forum.categories')
            ->with('success', 'تم إضافة القسم بنجاح');
    }

    public function editCategory(ForumCategory $category)
    {
        return view('admin.forum.categories.edit', compact('category'));
    }

    public function updateCategory(Request $request, ForumCategory $category)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'icon'       => 'nullable|image|max:1024',
            'sort_order' => 'integer|min:0',
            'is_active'  => 'boolean',
        ]);

        if ($request->hasFile('icon')) {
            $data['icon'] = $request->file('icon')->store('forum/icons', 'public');
        }

        $data['is_active'] = $request->boolean('is_active', false);

        $category->update($data);

        return redirect()->route('admin.forum.categories')
            ->with('success', 'تم تعديل القسم بنجاح');
    }

    public function destroyCategory(ForumCategory $category)
    {
        $category->delete();

        return redirect()->route('admin.forum.categories')
            ->with('success', 'تم حذف القسم');
    }

    // ── Sub-Categories ────────────────────────────────────────────────────

    public function subCategories(Request $request)
    {
        $query = ForumSubCategory::with('category')->orderBy('sort_order');

        if ($request->filled('category_id')) {
            $query->where('forum_category_id', $request->category_id);
        }

        $subCategories = $query->paginate(20)->withQueryString();
        $categories    = ForumCategory::active()->get();

        return view('admin.forum.sub_categories.index', compact('subCategories', 'categories'));
    }

    public function createSubCategory()
    {
        $categories = ForumCategory::active()->get();

        return view('admin.forum.sub_categories.create', compact('categories'));
    }

    public function storeSubCategory(Request $request)
    {
        $data = $request->validate([
            'forum_category_id' => 'required|exists:forum_categories,id',
            'name'              => 'required|string|max:255',
            'sort_order'        => 'integer|min:0',
            'is_active'         => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        ForumSubCategory::create($data);

        return redirect()->route('admin.forum.sub-categories')
            ->with('success', 'تم إضافة القسم الفرعي بنجاح');
    }

    public function editSubCategory(ForumSubCategory $subCategory)
    {
        $categories = ForumCategory::active()->get();

        return view('admin.forum.sub_categories.edit', compact('subCategory', 'categories'));
    }

    public function updateSubCategory(Request $request, ForumSubCategory $subCategory)
    {
        $data = $request->validate([
            'forum_category_id' => 'required|exists:forum_categories,id',
            'name'              => 'required|string|max:255',
            'sort_order'        => 'integer|min:0',
            'is_active'         => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', false);

        $subCategory->update($data);

        return redirect()->route('admin.forum.sub-categories')
            ->with('success', 'تم تعديل القسم الفرعي بنجاح');
    }

    public function destroySubCategory(ForumSubCategory $subCategory)
    {
        $subCategory->delete();

        return redirect()->route('admin.forum.sub-categories')
            ->with('success', 'تم حذف القسم الفرعي');
    }

    // ── Posts ─────────────────────────────────────────────────────────────

    public function posts(Request $request)
    {
        $query = ForumPost::with(['user', 'subCategory.category'])->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('sub_category_id')) {
            $query->where('forum_sub_category_id', $request->sub_category_id);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        $posts         = $query->paginate(20)->withQueryString();
        $subCategories = ForumSubCategory::with('category')->get();

        return view('admin.forum.posts.index', compact('posts', 'subCategories'));
    }

    public function showPost(ForumPost $post)
    {
        $post->load(['user', 'subCategory.category', 'replies.user']);

        return view('admin.forum.posts.show', compact('post'));
    }

    public function togglePostStatus(ForumPost $post)
    {
        $post->update(['is_active' => !$post->is_active]);

        return back()->with('success', 'تم تحديث حالة المنشور');
    }

    public function togglePostPin(ForumPost $post)
    {
        $post->update(['is_pinned' => !$post->is_pinned]);

        return back()->with('success', 'تم تحديث تثبيت المنشور');
    }

    public function destroyPost(ForumPost $post)
    {
        $post->delete();

        return redirect()->route('admin.forum.posts')
            ->with('success', 'تم حذف المنشور');
    }

    // ── Replies ───────────────────────────────────────────────────────────

    public function destroyReply(ForumReply $reply)
    {
        $postId = $reply->forum_post_id;
        $reply->delete();

        return redirect()->route('admin.forum.posts.show', $postId)
            ->with('success', 'تم حذف الرد');
    }

    public function toggleReplyStatus(ForumReply $reply)
    {
        $reply->update(['is_active' => !$reply->is_active]);

        return back()->with('success', 'تم تحديث حالة الرد');
    }
}
