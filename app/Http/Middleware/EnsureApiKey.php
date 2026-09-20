<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Requires a valid X-API-Key (or Authorization: Bearer) header on API routes.
 * The resolved key is attached to the request as 'api_key' for downstream use
 * (created_by auto-fill, idempotency binding).
 */
class EnsureApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $plainKey = $request->bearerToken()
            ?? $request->header('X-API-Key', null);

        if ($plainKey === null || $plainKey === '') {
            return $this->deny('unauthenticated', 'API key is required (X-API-Key header).');
        }

        $key = ApiKey::where('key_hash', ApiKey::hashKey($plainKey))->first();

        if ($key === null || ! $key->is_active) {
            return $this->deny('invalid_api_key', 'The provided API key is invalid or revoked.');
        }

        $key->markUsed();

        $request->attributes->set('api_key', $key);

        return $next($request);
    }

    private function deny(string $code, string $message): Response
    {
        return response()->json([
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
        ], 401);
    }
}
