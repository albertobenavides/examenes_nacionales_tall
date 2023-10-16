<?php

namespace App\Http\Controllers;

use App\Pregunta;
use App\Respuesta;
use Illuminate\Http\Request;
use App\Tema;
use Illuminate\Support\Facades\Auth;
use Carbon;
use stdClass;

class PreguntaController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('revisar.rol:1')->except(['show', 'edit', 'revisar']);
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(isset($request->preguntas)){ // Si es un archivo de preguntas
            $request->validate([
                'preguntas' => 'required|mimes:csv,txt',
            ]);
            if (($gestor = fopen($request->preguntas, "r")) !== FALSE) {
                $fila = 1;
                while (($datos = fgetcsv($gestor, 1000, "|")) !== FALSE) {
                    $total = count($datos);
                    $fila++;
                    for ($c=0; $c < $total - 1; $c++) { // Con total -1 se evita guardar la retroalimentación
                        if ($c == 0) { // Pregunta
                            $pregunta = new Pregunta;
                            $pregunta->contenido = $datos[0];
                            $pregunta->ayuda = $datos[$total - 1];
                            $pregunta->tema_id = $request->tema_id;
                            $pregunta->curso_id = $request->curso_id;
                            $pregunta->save();
                        } else{ // Respuestas
                            $respuesta = new Respuesta;
                            $respuesta->contenido = $datos[$c];
                            $respuesta->pregunta_id = $pregunta->id;
                            $respuesta->correcta = $c == 1 ? 1 : 0;
                            $respuesta->save();
                        }
                    }
                }
                return back()->with('exito', 'Preguntas guardadas');
            } else{ // Si no se abre bien el archivo

            }
        } else{ // Si es una sola pregunta
            $request->validate([
                'contenido' => 'required',
                'curso_id' => 'required'
            ]);
            $pregunta = new Pregunta;
            $pregunta->contenido = $request->contenido;
            $pregunta->ayuda = $request->ayuda;
            $pregunta->tema_id = $request->tema_id;
            $pregunta->curso_id = $request->curso_id;
            $pregunta->save();
    
            return back()->with('exito', 'Pregunta guardada');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Pregunta  $pregunta
     * @return \Illuminate\Http\Response
     */
    public function show($tema_id)
    {
        $tema = Tema::find($tema_id);
        if ($tema == null){
            return null;
        }
        
        if(Auth::user()->rol_id == 1 or (Auth::user()->pagos->where('curso_id', $tema->modulo->curso->id)->where('fin', '>=', Carbon\Carbon::today())->count() > 0 and Auth::user()->pagos->where('curso_id', $tema->modulo->curso->id)->where('fin', '>=', Carbon\Carbon::today())->sortByDesc('promo_id')->first()->promo->examenes == true)){
            // https://stillat.com/blog/2018/04/22/laravel-5-collections-retrieving-random-collection-elements-with-random
            $p = $tema->preguntas->random();
            $p->r = $p->respuestas()->select('id', 'contenido')->get()->shuffle();
            return $p;
        } else {
            return null;
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Pregunta  $pregunta
     * @return \Illuminate\Http\Response
     */
    public function edit($pregunta_id)
    {
        
    }

    public function revisar($pregunta_id){
        $pregunta = Pregunta::find($pregunta_id);
        $t = $pregunta->respuestas->where('correcta')->first();
        $t->ayuda = $pregunta->ayuda;
        return $t->only('id', 'ayuda');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Pregunta  $pregunta
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pregunta $pregunta)
    {
        if(isset($request->contenido)){
            $pregunta->contenido = $request->contenido;
        }
        if(isset($request->ayuda)){
            $pregunta->ayuda = $request->ayuda;
        }
        $pregunta->save();
        return back()->with('exito', 'Pregunta actualizada');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Pregunta  $pregunta
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pregunta $pregunta)
    {
        foreach ($pregunta->respuestas as $r) {
            $r->delete();
        }
        $pregunta->delete();

        return back()->with('exito', 'Pregunta borrada');
    }
}
