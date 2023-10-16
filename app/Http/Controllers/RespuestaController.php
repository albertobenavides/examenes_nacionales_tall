<?php

namespace App\Http\Controllers;

use App\Respuesta;
use App\Models\Pregunta;
use Illuminate\Http\Request;

class RespuestaController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('revisar.rol:1');
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
        $request->validate([
            'contenido' => 'required',
            'pregunta_id' => 'required'
        ]);
        if (isset($request->correcta)) {
            $pregunta = Pregunta::find($request->pregunta_id);
            foreach ($pregunta->respuestas as $r) {
                $r->correcta = 0;
                $r->save();
            }
        }
        $respuesta = new Respuesta;
        $respuesta->contenido = $request->contenido;
        $respuesta->pregunta_id = $request->pregunta_id;
        $respuesta->correcta = isset($request->correcta) ? 1 : 0;
        $respuesta->save();

        return back()->with('exito', 'Respuesta guardada');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Respuesta  $respuesta
     * @return \Illuminate\Http\Response
     */
    public function show(Respuesta $respuesta)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Respuesta  $respuesta
     * @return \Illuminate\Http\Response
     */
    public function edit(Respuesta $respuesta)
    {
        return view('respuestas.editar', [
            'respuesta' => $respuesta
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Respuesta  $respuesta
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Respuesta $respuesta)
    {
        if (isset($request->correcta)) {
            foreach ($respuesta->pregunta->respuestas as $r) {
                $r->correcta = 0;
                $r->save();
            }
        } else {
            $respuesta->correcta == 1;
            $r =$respuesta->pregunta->respuestas->first();
            $r->correcta = 1;
            $r->save();
        }
        $respuesta->correcta = isset($request->correcta) ? 1 : 0;
        $respuesta->contenido = $request->contenido;
        $respuesta->save();

        return redirect('/preguntas/editar/' . $respuesta->pregunta->id)->with('exito', 'Respuesta actualizada');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Respuesta  $respuesta
     * @return \Illuminate\Http\Response
     */
    public function destroy(Respuesta $respuesta)
    {
        $p = $respuesta->pregunta->id;
        $respuesta->delete();

        return redirect('/preguntas/editar/' . $p)->with('exito', 'Respuesta borrada');
    }
}
