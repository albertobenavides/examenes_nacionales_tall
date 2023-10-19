<?php

namespace App\Http\Controllers;

use App\Models\Prueba;
use App\Models\Intento;
use App\Models\Pregunta;
use App\Models\Respuesta;
use App\Models\Tema;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PruebaController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('revisar.rol:1')->except(['show', 'revisar', 'intentos', 'actualizar_respuestas']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function revisar(Request $request){
        $intento = Intento::find($request->intento_id);
        
        $rs = json_decode($intento->respuestas);
        $correctas = 0;
        foreach ($rs as $r) {
            $respuesta = Respuesta::find($r);
            $pregunta = $respuesta->pregunta;
            $c = $pregunta->respuestas->where('correcta')->first();
            if ($c->id == $respuesta->id){
                $correctas += 1;
            }
        }
        $total = count(json_decode($intento->preguntas));
        $calificacion = round( $correctas / $total * 100);

        $intento->calificacion = $calificacion;
        $intento->aciertos = $correctas;
        
        $intento->save();

        return redirect("/pruebas/$intento->prueba_id/intentos/$intento->id");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $prueba = new Prueba;
        $prueba->nombre = $request->nombreExamen;
        $prueba->descripcion = $request->descripcionExamen;
        $prueba->curso_id = $request->curso_id;
        $prueba->save();

        foreach ($prueba->curso->modulos as $m) {
            $prueba->modulos()->syncWithoutDetaching($m->id);
            foreach ($m->temas as $t) {
                $prueba->temas()->syncWithoutDetaching($t->id);
            }
        }

        return back()->with('exito', 'Examen creado');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Prueba  $prueba
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if($id > 0){
            $prueba = Prueba::find($id);
            if(Auth::user()->rol_id == 1 or (Auth::user()->pagos->where('curso_id', $prueba->curso->id)->where('fin', '>=', Carbon::today())->count() > 0 and Auth::user()->pagos->where('curso_id', $prueba->curso->id)->where('fin', '>=', Carbon::today())->sortByDesc('promo_id')->first()->promo->examenes == true)){
                $preguntas = Cache::get("preguntas_prueba$id", function () use ($prueba) {
                    return Pregunta::select('id', 'contenido', 'tema_id')->whereIn('tema_id', $prueba->temas->where('pivot.preguntas', '>', 0)->pluck('id'))->with('respuestas:id,contenido,pregunta_id')->get()->sortBy(function($order) use($prueba){
                        return array_search($order['tema_id'], $prueba->temas->pluck('id')->toArray());
                     });
                });
                // $preguntas->filter(function ($p) use ($prueba) { [ ] Aquí vamos
                //     return in_array($file->id, $existingIds);
                // });
                
                $intento = new Intento();
                $intento->prueba_id = $prueba->id;
                $intento->user_id = Auth::id();
                $intento->preguntas = json_encode($preguntas->pluck('id')->toArray());
                $intento->calificacion = -1;
                $intento->aciertos = -1;
                $intento->save();

                return view('pruebas.mostrar', [
                    'prueba' => $prueba,
                    'preguntas' => $preguntas,
                    'intento' => $intento
                ]);
            } else {
                return redirect('/pagos/crear?curso_id=' . $prueba->curso->id);
            }
        } else{
            $id = $id * -1;
            $tema = Tema::find($id);
            if(Auth::user()->rol_id == 1 or (Auth::user()->pagos->where('curso_id', $tema->modulo->curso->id)->where('fin', '>=', Carbon::today())->count() > 0 and Auth::user()->pagos->where('curso_id', $tema->modulo->curso->id)->where('fin', '>=', Carbon::today())->sortByDesc('promo_id')->first()->promo->examenes == true)){
                $preguntas = Cache::get("preguntas_prueba$id", function () use ($tema) {
                    return Pregunta::select('id', 'contenido')->whereIn('id', $tema->preguntas->pluck('id'))->with('respuestas:id,contenido,pregunta_id')->get();
                });
                $preguntas = $preguntas->take($tema->preguntar);
                $prueba = new Prueba;
                $prueba->id = $tema->id * -1;
                $prueba->nombre = $tema->nombre;
                $prueba->curso_id = $tema->modulo->curso->id;

                $intento = new Intento();
                $intento->prueba_id = $prueba->id;
                $intento->user_id = Auth::id();
                $intento->preguntas = json_encode($preguntas->pluck('id')->toArray());
                $intento->calificacion = -1;
                $intento->aciertos = -1;
                $intento->save();

                return view('pruebas.mostrar', [
                    'prueba' => $prueba,
                    'preguntas' => $preguntas,
                    'intento' => $intento
                ]);
            } else{
                return redirect('/pagos/crear');
            }
        }
        return view('pruebas.mostrar', [
            'prueba' => $id
        ]);
    }

    public function actualizar_respuestas($intento_id, $respuesta_id){
        $intento = Intento::find($intento_id);
        $respuesta = Respuesta::find($respuesta_id);
        if ($respuesta != null && $intento != null){
            $to_delete = $respuesta->pregunta->respuestas->pluck('id')->toArray();
            $current = json_decode($intento->respuestas);
            if ($current == null){
                $current = [];
            }
            // https://stackoverflow.com/a/1065280
            $current = array_values(array_diff($current, $to_delete));
            $current[] = $respuesta_id;
            $intento->respuestas = json_encode($current);
            $intento->save();
            
            return $intento->respuestas;
        }
    }

    public function intentos($id){
        $prueba = Prueba::find($id);
        if ($prueba == null){
            return redirect('/inicio')->with('mensaje', 'Esa página no existe');
        }
        if(Auth::user()->rol_id == 1 || (Auth::user()->pagos->where('curso_id', $prueba->curso->id)->where('fin', '>=', Carbon::today())->count() > 0 and Auth::user()->pagos->where('curso_id', $prueba->curso->id)->where('fin', '>=', Carbon::today())->sortByDesc('promo_id')->first()->promo->examenes == true)){
            $intentos = Intento::where('prueba_id', $prueba->id)->where('user_id', Auth::id())->where('calificacion', '>', -1)->get();
            return view('pruebas.intentos', [
                'prueba' => $prueba,
                'intentos' => $intentos
            ]);
        } else {
            return redirect('/pagos/crear');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Prueba  $prueba
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $prueba = Prueba::find($id);
        return view('pruebas.editar', [
            'examen' => $prueba
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Prueba  $prueba
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $prueba_id)
    {
        if($prueba_id != -1){
            $prueba = Prueba::find($prueba_id);
            if($request->modulo_id){
                $prueba->modulos()->updateExistingPivot($request->modulo_id, ['preguntas' => $request->preguntas]);
                return "Preguntas actualizadas";
            }
            if($request->tema_id){
                $prueba->temas()->updateExistingPivot($request->tema_id, ['preguntas' => $request->preguntas]);
                return "Preguntas actualizadas";
            }
            $prueba->nombre = $request->nombre;
            $prueba->descripcion = $request->descripcion;
            $prueba->save();
            return redirect("/examenes/$prueba->id/editar")->with("exito", "Prueba actualizada");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Prueba  $prueba
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $prueba = Prueba::find($id);
        $curso_id = $prueba->curso->id;
        if ($prueba != null){
            $prueba->modulos()->detach();
            $prueba->temas()->detach();
            $prueba->delete();
    
            return redirect("/cursos/$curso_id/editar")->with('exito', 'Examen eliminado');
        } else {
            return redirect("/cursos/$curso_id/editar")->with('error', 'Ese examen no existe');
        }
    }
}
