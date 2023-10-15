<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Carbon\Carbon;

class RevisarPago
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
        if(Auth::user()->rol_id == 1){
            return $next($request);
        }
        else if(Auth::user()->pagos->where('curso_id', $request->curso_id)->where('fin', '>=', Carbon::today())->count() == 0){
            return redirect('/pagos/crear?curso_id=' . $request->curso_id);
        } else{
            return $next($request);
        }
    }
}
