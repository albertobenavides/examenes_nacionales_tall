<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Intento;
use App\Models\Pago;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalificacionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('revisar.rol:1,3');
    }

    public function index()
    {
        return view('calificaciones.index');
    }

    public function index_simulaciones()
    {
        return view('calificaciones.simulaciones');
    }

    public function usuarios(Request $request)
    {
        if (isset($request->curso_id)){
            $usuarios = Pago::where('promo_id', '>=', 3)->where('curso_id', $request->curso_id)->where('fin', '>=', Carbon::today())->paginate(5)->pluck('usuario')->flatten()->unique()->pluck('id');
            $modulos = Curso::find($request->curso_id)->modulos->sortBy('orden')->pluck('nombre');
            $r = ['usuarios' => $usuarios, 'modulos' => $modulos];
            return $r;
        }
    }

    public function simulaciones(Request $request)
    {
        if (isset($request->curso_id)){
            $simulaciones = Curso::find($request->curso_id)->pruebas;
            return $simulaciones;
        } else if (isset($request->simulacion_id)){
            $intentos_alumno = Intento::where('prueba_id', $request->simulacion_id)->where('calificacion', '>', -1)->get()->groupBy('user_id');
            $as = [];
            foreach ($intentos_alumno as $ia) {
                $t = [
                    'nombre' => $ia->first()->usuario->name,
                    'minimo' => $ia->min('calificacion'),
                    'maximo' => $ia->max('calificacion'),
                    'promedio' => $ia->avg('calificacion'),
                    'total' => $ia->count()
                ];
                $as[] = $t;
            }
            return $as;
        }
    }

    public function calificaciones(Request $request)
    {
        if (isset($request->curso_id) && $request->user_id){

            $u = User::find($request->user_id);
            
            $gran_totales = 0.0;
            $gran_pasados = 0.0;
            $gran_val = 0;
            $modulos = [];
            foreach (Curso::find($request->curso_id)->modulos->sortBy('orden') as $m) {
                $totales = 0.0;
                $pasados = 0.0;
                $val = 0;
                foreach ($m->temas->where('preguntar', '>', 0) as $t) {
                    $totales = $totales + 1;
                    $gran_totales = $gran_totales + 1;
                    $max = Intento::where('user_id', $u->id)->where('prueba_id', $t->id * -1)->where('calificacion', '>', -1)->max('calificacion') ?? 'NA';
                    $pasados = $max >= 90 ? $pasados + 1 : $pasados;
                    $gran_pasados = $max >= 90 ? $gran_pasados + 1 : $gran_pasados;
                }
                if ($totales <= 0){
                    $val = 0;
                } else {
                    $val = intval($pasados / $totales * 100);
                }
                $modulos[] = $val;
            }
            $u->modulos = $modulos;
            if ($gran_totales > 0){
                $gran_val = intval($gran_pasados / $gran_totales * 100);
            }
            $u->global = $gran_val;

            return $u;
        }
    }
}
