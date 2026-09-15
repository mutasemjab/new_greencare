<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiClient;
use Illuminate\Http\Request;

class ApiClientController extends Controller
{
    public function index()
    {
        $apiClients = ApiClient::latest()->get();

        return view('admin.api-clients.index', compact('apiClients'));
    }

    public function create()
    {
        return view('admin.api-clients.create');
    }

    public function store(Request $request)
    {
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
        $apiClient->update(['is_active' => ! $apiClient->is_active]);

        return redirect()->route('admin.api-clients.index')
            ->with('success', $apiClient->is_active ? 'تم تفعيل المفتاح' : 'تم إلغاء تفعيل المفتاح');
    }

    public function destroy(ApiClient $apiClient)
    {
        $apiClient->delete();

        return redirect()->route('admin.api-clients.index')
            ->with('success', 'تم حذف المفتاح');
    }
}
