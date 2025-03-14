<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsStudent
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->role->role === "student") {
            return $next($request);
        }

        return response()->json(['message' => 'Only students are allowed to enroll.'], 403);
    }
}
