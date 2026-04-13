<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ApiKey;
use App\Models\User;

class ValidateApiKey {
    public function handle(Request $request, Closure $next) {
        $apiKey = $request->header('X-API-Key') ?: $request->input('api_key');

        if (!$apiKey) {
            return response()->json(['error' => 'API key is required'], 401);
        }

        $apiKeyRecord = ApiKey::where('key', $apiKey)
            ->where('is_active', true)
            ->first();

        if (!$apiKeyRecord) {
            return response()->json(['error' => 'Invalid API key'], 401);
        }

        $user = User::find($apiKeyRecord->user_id);
        
        if (!$user || !$user->is_active) {
            return response()->json(['error' => 'User not found or inactive'], 401);
        }

        // Update last used time
        $apiKeyRecord->update(['last_used_at' => now()]);

        // Attach user to request
        auth('api')->setUser($user);

        return $next($request);
    }
}
