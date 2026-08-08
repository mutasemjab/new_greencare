<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LabCategory;
use App\Models\LabRequest;
use App\Models\LabTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LabController extends Controller
{
    // ── Categories ────────────────────────────────────────────────────────

    public function categories(Request $request)
    {
        $query = LabCategory::withCount('tests')->orderBy('sort_order');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $categories = $query->paginate(20)->withQueryString();

        return view('admin.lab.categories.index', compact('categories'));
    }

    public function createCategory()
    {
        return view('admin.lab.categories.create');
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
            $data['icon'] = $request->file('icon')->store('lab/icons', 'public');
        }

        $data['is_active'] = $request->boolean('is_active', true);

        LabCategory::create($data);

        return redirect()->route('admin.lab.categories')
            ->with('success', 'تم إضافة الفئة بنجاح');
    }

    public function editCategory(LabCategory $category)
    {
        return view('admin.lab.categories.edit', compact('category'));
    }

    public function updateCategory(Request $request, LabCategory $category)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'icon'       => 'nullable|image|max:1024',
            'sort_order' => 'integer|min:0',
            'is_active'  => 'boolean',
        ]);

        if ($request->hasFile('icon')) {
            Storage::disk('public')->delete($category->icon);
            $data['icon'] = $request->file('icon')->store('lab/icons', 'public');
        }

        $data['is_active'] = $request->boolean('is_active', false);

        $category->update($data);

        return redirect()->route('admin.lab.categories')
            ->with('success', 'تم تعديل الفئة بنجاح');
    }

    public function destroyCategory(LabCategory $category)
    {
        Storage::disk('public')->delete($category->icon);
        $category->delete();

        return redirect()->route('admin.lab.categories')
            ->with('success', 'تم حذف الفئة');
    }

    // ── Tests ─────────────────────────────────────────────────────────────

    public function tests(Request $request)
    {
        $query = LabTest::with('category')->latest();

        if ($request->filled('category_id')) {
            $query->where('lab_category_id', $request->category_id);
        }

        $tests      = $query->paginate(20)->withQueryString();
        $categories = LabCategory::active()->get();

        return view('admin.lab.tests.index', compact('tests', 'categories'));
    }

    public function createTest()
    {
        $categories = LabCategory::active()->get();

        return view('admin.lab.tests.create', compact('categories'));
    }

    public function storeTest(Request $request)
    {
        $data = $request->validate([
            'lab_category_id' => 'required|exists:lab_categories,id',
            'name'            => 'required|string|max:255',
            'description'     => 'nullable|string',
            'price'           => 'required|numeric|min:0',
            'is_active'       => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        LabTest::create($data);

        return redirect()->route('admin.lab.tests')
            ->with('success', 'تم إضافة الفحص بنجاح');
    }

    public function editTest(LabTest $test)
    {
        $categories = LabCategory::active()->get();

        return view('admin.lab.tests.edit', compact('test', 'categories'));
    }

    public function updateTest(Request $request, LabTest $test)
    {
        $data = $request->validate([
            'lab_category_id' => 'required|exists:lab_categories,id',
            'name'            => 'required|string|max:255',
            'description'     => 'nullable|string',
            'price'           => 'required|numeric|min:0',
            'is_active'       => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', false);

        $test->update($data);

        return redirect()->route('admin.lab.tests')
            ->with('success', 'تم تعديل الفحص بنجاح');
    }

    public function destroyTest(LabTest $test)
    {
        $test->delete();

        return redirect()->route('admin.lab.tests')
            ->with('success', 'تم حذف الفحص');
    }

    // ── Requests ──────────────────────────────────────────────────────────

    public function requests(Request $request)
    {
        $query = LabRequest::with(['user', 'tests.test'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('patient_code', 'like', "%{$request->search}%")
                  ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
        }

        $requests = $query->paginate(20)->withQueryString();

        return view('admin.lab.requests.index', compact('requests'));
    }

    public function showRequest(LabRequest $request)
    {
        $request->load(['user', 'address', 'tests.test.category']);

        return view('admin.lab.requests.show', compact('request'));
    }

    public function updateRequestStatus(Request $httpRequest, LabRequest $request)
    {
        $httpRequest->validate([
            'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled',
        ]);

        $request->update(['status' => $httpRequest->status]);

        return back()->with('success', 'تم تحديث الحالة بنجاح');
    }
}
