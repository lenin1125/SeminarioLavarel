<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarRol
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $rol): Response
    {
        // 1. Verificamos si el usuario inició sesión
        if (!auth()->check()) {
            return redirect('login');
        }

        // 2. Verificamos si el rol del usuario coincide con el requerido
        if (auth()->user()->rol !== $rol) {
            abort(403, 'No tienes permiso para acceder a esta sección (Se requiere rol: ' . $rol . ').');
        }

        return $next($request);
    }
}