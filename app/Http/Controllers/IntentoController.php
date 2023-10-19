<?php

namespace App\Http\Controllers;

use App\Models\Intento;
use App\Models\Pregunta;
use App\Models\Prueba;
use App\Models\Respuesta;
use App\Models\Tema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IntentoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
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
        $intento = new Intento;
        $intento->user_id = Auth::id();
        $intento->prueba_id = $request->prueba_id;
        $intento->calificacion = $request->calificacion;
        $intento->aciertos = $request->aciertos;
        $intento->preguntas = json_encode($request->preguntas);
        $intento->respuestas = json_encode($request->respuestas);
        $intento->save();

        return $intento->id;
    }

    public function revision($prueba_id, $intento_id){
        $prueba = Prueba::find($prueba_id);
        $intento = Intento::find($intento_id);

        if ($intento == null){
            return redirect('/inicio')->with('mensaje', 'No existe');
        }

        if ($intento->user_id != Auth::id()){
            return redirect('/inicio')->with('mensaje', 'Acceso denegado');
        }

        if ($intento->calificacion < 0 || $intento->aciertos < 0){
            return redirect('/inicio')->with('mensaje', 'No existe');
        }

        if ($prueba == null){
            $prueba = new Prueba;
            $tema = Tema::find($prueba_id * -1);
            $prueba->nombre = $tema->nombre;
            $prueba->curso_id = $tema->modulo->curso_id;
        }
        
        $preguntas = Pregunta::find(json_decode($intento->preguntas));
        $respuestas = Respuesta::find($intento->respuestas);

        
        return view('pruebas.revision', [
            'prueba' => $prueba,
            'intento' => $intento,
            'preguntas' => $preguntas,
            'respuestas' => $respuestas
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Intento  $intento
     * @return \Illuminate\Http\Response
     */
    public function show(Intento $intento)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Intento  $intento
     * @return \Illuminate\Http\Response
     */
    public function edit(Intento $intento)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Intento  $intento
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Intento $intento)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Intento  $intento
     * @return \Illuminate\Http\Response
     */
    public function destroy(Intento $intento)
    {
        //
    }
}
