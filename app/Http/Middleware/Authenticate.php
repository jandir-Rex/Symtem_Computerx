<?php
// filepath: app/Http/Middleware/Authenticate.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        if (Auth::guard($guards[0] ?? null)->guest()) {
            return redirect()->route('login');
        }
        return $next($request);
    }
}