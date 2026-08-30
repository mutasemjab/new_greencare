<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\XrayCategory;
use App\Models\XrayRequest;
use App\Models\XrayTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class XrayController extends Controller
{
    // ── Categories ────────────────────────────────────────────────────────

    public function categories(Request $request)
    {
        $query = XrayCategory::withCount('tests')->orderBy('sort_order');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $categories = $query->paginate(20)->withQueryString();

        return view('admin.xray.categories.index', compact('categories'));
    }

    public function createCategory()
    {
        return view('admin.xray.categories.create');
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
            $data['icon'] = $request->file('icon')->store('xray/icons', 'public');
        }

        $data['is_active'] = $request->boolean('is_active', true);

        XrayCategory::create($data);

        return redirect()->route('admin.xray.categories')
            ->with('success', 'تم إضافة الفئة بنجاح');
    }

    public function editCategory(XrayCategory $category)
    {
        return view('admin.xray.categories.edit', compact('category'));
    }

    public function updateCategory(Request $request, XrayCategory $category)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'icon'       => 'nullable|image|max:1024',
            'sort_order' => 'integer|min:0',
            'is_active'  => 'boolean',
        ]);

        if ($request->hasFile('icon')) {
            Storage::disk('public')->delete($category->icon);
            $data['icon'] = $request->file('icon')->store('xray/icons', 'public');
        }

        $data['is_active'] = $request->boolean('is_active', false);

        $category->update($data);

        return redirect()->route('admin.xray.categories')
            ->with('success', 'تم تعديل الفئة بنجاح');
    }

    public function destroyCategory(XrayCategory $category)
    {
        Storage::disk('public')->delete($category->icon);
        $category->delete();

        return redirect()->route('admin.xray.categories')
            ->with('success', 'تم حذف الفئة');
    }

    // ── Tests ─────────────────────────────────────────────────────────────

    public function tests(Request $request)
    {
        $query = XrayTest::with('category')->latest();

        if ($request->filled('category_id')) {
            $query->where('xray_category_id', $request->category_id);
        }

        $tests      = $query->paginate(20)->withQueryString();
        $categories = XrayCategory::active()->get();

        return view('admin.xray.tests.index', compact('tests', 'categories'));
    }

    public function createTest()
    {
        $categories = XrayCategory::active()->get();

        return view('admin.xray.tests.create', compact('categories'));
    }

    public function storeTest(Request $request)
    {
        $data = $request->validate([
            'xray_category_id' => 'required|exists:xray_categories,id',
            'name'             => 'required|string|max:255',
            'description'      => 'nullable|string',
            'price'            => 'required|numeric|min:0',
            'is_active'        => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        XrayTest::create($data);

        return redirect()->route('admin.xray.tests')
            ->with('success', 'تم إضافة الأشعة بنجاح');
    }

    public function editTest(XrayTest $test)
    {
        $categories = XrayCategory::active()->get();

        return view('admin.xray.tests.edit', compact('test', 'categories'));
    }

    public function updateTest(Request $request, XrayTest $test)
    {
        $data = $request->validate([
            'xray_category_id' => 'required|exists:xray_categories,id',
            'name'             => 'required|string|max:255',
            'description'      => 'nullable|string',
            'price'            => 'required|numeric|min:0',
            'is_active'        => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', false);

        $test->update($data);

        return redirect()->route('admin.xray.tests')
            ->with('success', 'تم تعديل الأشعة بنجاح');
    }

    public function destroyTest(XrayTest $test)
    {
        $test->delete();

        return redirect()->route('admin.xray.tests')
            ->with('success', 'تم حذف الأشعة');
    }

    // ── Requests ──────────────────────────────────────────────────────────

    public function requests(Request $request)
    {
        $query = XrayRequest::with(['user', 'tests.test'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('patient_code', 'like', "%{$request->search}%")
                  ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
        }

        $requests = $query->paginate(20)->withQueryString();

        return view('admin.xray.requests.index', compact('requests'));
    }

    public function showRequest(XrayRequest $request)
    {
        $request->load(['user', 'address', 'tests.test.category']);

        return view('admin.xray.requests.show', compact('request'));
    }

    public function updateRequestStatus(Request $httpRequest, XrayRequest $request)
    {
        $httpRequest->validate([
            'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled',
        ]);

        $request->update(['status' => $httpRequest->status]);

        return back()->with('success', 'تم تحديث الحالة بنجاح');
    }

    public function uploadResult(Request $httpRequest, XrayRequest $request)
    {
        $httpRequest->validate([
            'result_file' => 'required|file|mimes:pdf|max:10240',
        ], [
            'result_file.mimes' => 'يجب أن يكون الملف بصيغة PDF فقط',
        ]);

        if ($request->result_file) {
            Storage::disk('public')->delete($request->result_file);
        }

        $path = $httpRequest->file('result_file')->store('xray-results', 'public');

        $request->update(['result_file' => $path]);

        return back()->with('success', 'تم رفع نتيجة الأشعة بنجاح');
    }
}
