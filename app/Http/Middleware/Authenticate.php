<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        if (! $user = Auth::guard('api')->user()) {
            return response()->json([
                'status' => false,
                'message' => 'Authentication failed',
                'data' => null
            ], 401);
        }

        return $next($request);
    }
}

