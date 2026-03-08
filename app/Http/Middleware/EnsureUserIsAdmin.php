<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    private const ALLOWED_IDS = [1, 2, 3];

    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !in_array((int) $request->user()->id, self::ALLOWED_IDS, true)) {
            abort(403, 'Nincs jogosultságod az admin felület eléréséhez.');
        }

        return $next($request);
    }
}
