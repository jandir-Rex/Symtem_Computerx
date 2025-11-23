<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerificarPermiso
{
    public function handle(Request $request, Closure $next)
    {
        // Aquí puedes agregar tu lógica de permisos, por ahora solo deja pasar:
        return $next($request);
    }
}
