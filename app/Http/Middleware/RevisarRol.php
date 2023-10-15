<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class RevisarRol
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $rol, $rol1 = 1)
    {
        if(Auth::user()->rol_id == $rol || Auth::user()->rol_id == $rol1){
            return $next($request);
        } else {
            return redirect('/inicio')->with('mensaje', 'Acceso restringido');
        }
    }
}
