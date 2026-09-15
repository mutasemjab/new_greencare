<?php

namespace App\Http\Middleware;

use App\Models\ApiClient;
use Closure;
use Illuminate\Http\Request;

class VerifyApiKey
{
    /**
     * Authenticates external-platform requests via a static API key sent
     * in the X-API-Key header — separate from the mobile app's per-user
     * Passport tokens and the admin panel's session auth.
     */
    public function handle(Request $request, Closure $next)
    {
        $key = $request->header('X-API-Key');

        if (! $key) {
            return response()->json([
                'success' => false,
                'message' => 'مفتاح API مفقود، أرسله بترويسة X-API-Key',
            ], 401);
        }

        $client = ApiClient::active()->where('key_hash', hash('sha256', $key))->first();

        if (! $client) {
            return response()->json([
                'success' => false,
                'message' => 'مفتاح API غير صالح',
            ], 401);
        }

        $client->update(['last_used_at' => now()]);

        $request->attributes->set('api_client', $client);

        return $next($request);
    }
}
