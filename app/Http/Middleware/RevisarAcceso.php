<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RevisarAcceso
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //if (env('ACCESO', false) || (Auth::user() && Auth::user()->rol_id == 3)) {
        if (env('ACCESO', false)) {
            return redirect('/')->with('error', 'Acceso restringido');
        } else {
            return $next($request);
        }
    }
}
