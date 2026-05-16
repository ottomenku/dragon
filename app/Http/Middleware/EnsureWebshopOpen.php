<?php

namespace App\Http\Middleware;

use App\Models\WebshopSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureWebshopOpen
{
    public function handle(Request $request, Closure $next): Response
    {
        if (WebshopSetting::userMayAccess($request->user())) {
            return $next($request);
        }

        $message = 'Sajnáljuk, a webshop jelenleg nem üzemel.';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], 503);
        }

        return response()->view('webshop.closed', [], 503);
    }
}
