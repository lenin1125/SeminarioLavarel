<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Si el usuario está logueado pero NO es el admin, lo bloqueamos
        if ($request->user() && $request->user()->email !== 'admin@sneakerslh.com') {
            abort(403, 'Acceso denegado. Solo administradores.');
        }

        return $next($request);
    }
}