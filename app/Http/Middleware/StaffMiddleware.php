<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StaffMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ($user->role !== 'staff' && $user->role !== 'admin')) {
            return response()->json([
                'message' => 'Forbidden.',
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
