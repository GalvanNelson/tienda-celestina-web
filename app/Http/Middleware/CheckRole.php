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
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Si no está logueado, fuera.
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // 2. Obtenemos su rol (gracias a tu modelo User.php ya lo tenemos fácil)
        $userRole = $request->user()->role_type;

        // 3. Verificamos si su rol está en la lista permitida para esta ruta
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // 4. Si no tiene permiso, error 403
        abort(403, 'No tienes permiso para ver esta sección.');
    }
}
