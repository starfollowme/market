<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-Key') ?? $request->query('api_key');

        if (! $apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API key required. Send header X-API-Key.',
            ], 401);
        }

        $record = ApiKey::findByPlainKey($apiKey);

        if (! $record) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or inactive API key.',
            ], 401);
        }

        $record->touchLastUsed();
        $request->attributes->set('api_key', $record);

        return $next($request);
    }
}
