<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectBasedOnRole
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Mapeo de roles a sus dashboards
            $roleDashboards = [
                'admin' => 'dashboard.admin',
                'contador' => 'dashboard.contador',
                'vendedor_stand1' => 'dashboard.stand1',
                'vendedor_stand2' => 'dashboard.stand2',
                'almacen' => 'dashboard.almacen',
                'tecnico' => 'dashboard.stand2', // Los técnicos van al stand 2
            ];
            
            $role = $user->getRoleNames()->first();
            
            // Si está intentando acceder al dashboard genérico
            if ($request->routeIs('dashboard') && isset($roleDashboards[$role])) {
                return redirect()->route($roleDashboards[$role]);
            }
        }
        
        return $next($request);
    }
}