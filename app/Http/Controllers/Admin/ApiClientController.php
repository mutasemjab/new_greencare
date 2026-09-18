<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiClient;
use Illuminate\Http\Request;

class ApiClientController extends Controller
{
    public function index()
    {
        if (!auth()->user()->can('api-client-table')) {
            abort(403);
        }

        $apiClients = ApiClient::latest()->get();

        return view('admin.api-clients.index', compact('apiClients'));
    }

    public function create()
    {
        if (!auth()->user()->can('api-client-add')) {
            abort(403);
        }

        return view('admin.api-clients.create');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('api-client-add')) {
            abort(403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $key = ApiClient::generateKey();

        ApiClient::create([
            'name'      => $data['name'],
            'key_hash'  => $key['hash'],
            'is_active' => true,
        ]);

        // The raw key is only ever available right here — it's never
        // stored, so it can't be shown again after this redirect.
        return redirect()->route('admin.api-clients.index')
            ->with('success', 'تم إنشاء المفتاح بنجاح')
            ->with('generated_key', $key['raw']);
    }

    public function toggle(ApiClient $apiClient)
    {
        if (!auth()->user()->can('api-client-edit')) {
            abort(403);
        }

        $apiClient->update(['is_active' => ! $apiClient->is_active]);

        return redirect()->route('admin.api-clients.index')
            ->with('success', $apiClient->is_active ? 'تم تفعيل المفتاح' : 'تم إلغاء تفعيل المفتاح');
    }

    public function destroy(ApiClient $apiClient)
    {
        if (!auth()->user()->can('api-client-delete')) {
            abort(403);
        }

        $apiClient->delete();

        return redirect()->route('admin.api-clients.index')
            ->with('success', 'تم حذف المفتاح');
    }
}
