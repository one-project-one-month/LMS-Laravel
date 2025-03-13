<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::user()->role_id === get_role_id("admin")) {
            return $next($request);
        } else {
            return response()->json([
                "message" => "You are not authorized to suspend a user.",

            ], 403);
        }
    }
}
