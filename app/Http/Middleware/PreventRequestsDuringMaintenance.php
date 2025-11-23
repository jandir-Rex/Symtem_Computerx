<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventRequestsDuringMaintenance
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Lógica para manejar solicitudes durante el mantenimiento
        return $next($request);
    }
}