<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión');
        }

        $user = auth()->user();

        // Si el usuario es Administrador, tiene acceso total
        if ($user->hasRole('Administrador')) {
            return $next($request);
        }

        // Verificar si el usuario tiene alguno de los roles permitidos
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        // Si no tiene el rol, redirigir al dashboard con error
        return redirect()->route('dashboard.index')
            ->with('error', '❌ No tienes permisos para acceder a esta sección');
    }
}