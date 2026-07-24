<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Si el usuario está logueado y su rol_id es 1 (Admin), le permitimos pasar
        if (Auth::check() && Auth::user()->rol_id == 1) {
            return $next($request);
        }

        // Si no, lo redirigimos a la página principal con un aviso de error
        return redirect('/')->with('error', 'Acceso denegado. No tienes permisos de administrador.');
    }
}