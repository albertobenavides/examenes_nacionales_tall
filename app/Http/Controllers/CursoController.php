<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Modulo;
use App\Pregunta;
use App\Respuesta;
use App\Tema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CursoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['show', 'seccion']);
        $this->middleware('revisar.rol:1')->except(['show', 'seccion']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('cursos.index_admin', [
            'cursos' => Curso::all()
        ]);
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
            'nombre' => 'required',
            'examen_id' => 'required'
        ]);

        $curso = new Curso;
        $curso->nombre = $request->nombre;
        $curso->descripcion = $request->descripcion;
        if ($request->imagen) { // Si se especifica una imagen de avatar
            $curso->imagen = $request->file('imagen')->store('cursos', 'public'); // Se guarda la nueva
        }
        $curso->examen_id = $request->examen_id;
        $curso->save();

        return back()->with('exito', 'Curso ' . $curso->nombre . ' creado');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Curso  $curso
     * @return \Illuminate\Http\Response
     */
    public function show(Curso $curso)
    {
        if($curso == null || ($curso->activo == 0)){
            return redirect('/cursos')->with('mensaje', 'Este curso no existe');
        } else{
            return redirect("/cursos/$curso->id/clases");
        }
    }

    public function seccion($curso_id, $seccion)
    {
        $curso = Curso::find($curso_id);
        if($curso == null || ($curso->activo == 0)){
            return redirect('/cursos')->with('mensaje', 'Este curso no existe');
        } else{
            return view("cursos.mostrar_$seccion", [
                'curso' => $curso
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Curso  $curso
     * @return \Illuminate\Http\Response
     */
    public function edit(Curso $curso)
    {
        return view('cursos.editar', [
            'curso' => $curso
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Curso  $curso
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Curso $curso)
    {
        $request->validate([
            'nombre' => 'required',
        ]);

        $curso->nombre = $request->nombre;
        $curso->descripcion = $request->descripcion;
        if ($request->imagen) {
            if($curso->imagen != null){
                Storage::disk('public')->delete($curso->imagen);
                $curso->imagen = null;
            }
            $curso->imagen = $request->file('imagen')->store('cursos', 'public');
        } elseif(isset($request->eliminarImagen)){
            Storage::disk('public')->delete($curso->imagen);
            $curso->imagen = null;
        }
        $curso->examen_id = $request->examen_id;
        $curso->activo = isset($request->activo) ? 1 : 0;
        $curso->save();

        return redirect("/cursos/$curso->id/editar")->with('exito', "Curso $curso->nombre actualizado");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Curso  $curso
     * @return \Illuminate\Http\Response
     */
    public function destroy(Curso $curso)
    {
        foreach ($curso->pruebas as $p) {
            $p->modulos()->detach();
            $p->temas()->detach();
            $p->delete();
        }

        // Se obtienen en orden

        $modulos = Modulo::where('curso_id', $curso->id);
        if ($modulos->count() == 0){
            $temas = Tema::where('modulo_id', -1);
        } else {
            $temas = Tema::where('modulo_id', $modulos->pluck('id'));
        }
        if ($temas->count() == 0){
            $preguntas = Pregunta::where('tema_id', -1);
        } else {
            $preguntas = Pregunta::where('tema_id', $temas->pluck('id'));
        }
        if ($preguntas->count() == 0){
            $respuestas = Respuesta::where('pregunta_id', -1);
        } else {
            $respuestas = Respuesta::where('pregunta_id', $preguntas->pluck('id'));
        }
        
        // Se borran al revés
        if ($respuestas->count() > 0) $respuestas->delete();
        if ($preguntas->count() > 0) $preguntas->delete();
        foreach ($temas as $t) {
            Storage::delete($t->pdf);
            Storage::delete($t->video);
        }
        if ($temas->count() > 0) $temas->delete();
        if ($modulos->count() > 0) $modulos->delete();
        $curso->delete();

        return redirect("/cursos")->with('exito', 'Curso eliminado.');
    }
}
