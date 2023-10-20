<?php

namespace App\Http\Controllers;

use App\Models\Tema;
use App\Models\Modulo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TemaController extends Controller
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
            'nombre' => 'required',
            'modulo_id' => 'required'
        ]);

        $tema = new Tema;
        $tema->nombre = $request->nombre;
        $tema->descripcion = $request->descripcion;
        
        if ($request->imagen) {
            $tema->imagen = $request->file('imagen')->store('temas', 'public');
        }

        if ($request->pdfTema) { // Si se especifica una imagen de avatar
            $tema->pdf = $request->file('pdfTema')->store('pdf'); // Se guarda la nueva
        }
        if ($request->youtube){
            $tema->video = $request->youtube;
        }
        elseif ($request->videoTema) { // Si se especifica una imagen de avatar
            $tema->video = $request->file('videoTema')->store('video'); // Se guarda la nueva
        }
        $tema->orden = Modulo::find($request->modulo_id)->temas->count() + 1;
        $tema->modulo_id = $request->modulo_id;
        $tema->save();

        foreach ($tema->modulo->curso->pruebas as $p) {
            $tema->pruebas()->syncWithoutDetaching($p->id);
        }

        return back()->with('exito', "Tema $tema->nombre creado");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tema  $tema
     * @return \Illuminate\Http\Response
     */
    public function show(Tema $tema)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Tema  $tema
     * @return \Illuminate\Http\Response
     */
    public function edit(Tema $tema)
    {
        return view('temas.editar', [
            'tema' => $tema
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tema  $tema
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tema $tema)
    {
        if(isset($request->temas)){
            for ($i=0; $i < count($request->temas); $i++) {
                $tema = Tema::find($request->temas[$i]);
                $tema->orden = $i;
                $tema->save();
            }
        } else{
            $request->validate([
                'nombre' => 'required'
            ]);

            $tema->nombre = $request->nombre;
            $tema->descripcion = $request->descripcion;
            $tema->preguntar = $request->preguntar;
            
            if ($request->imagen) {
                Storage::disk('public')->delete($tema->imagen);
                $tema->imagen = $request->file('imagen')->store('temas', 'public');
            } elseif (isset($request->eliminarImagen)){
                Storage::disk('public')->delete($tema->imagen);
                $tema->imagen = null;
            }

            if ($request->pdfTema) {
                Storage::delete($tema->pdf);
                $tema->pdf = $request->file('pdfTema')->store('pdf'); // Se guarda la nueva
            } elseif (isset($request->eliminarPDF)){
                Storage::delete($tema->pdf);
                $tema->pdf = null;
            }
            
            if (isset($request->eliminarVideo)){
                Storage::delete($tema->video);
                $tema->video = null;
            }
            if($request->youtube != ''){
                Storage::delete($tema->video);
                $tema->video = $request->youtube;
            } else if ($request->video) {
                Storage::delete($tema->video);
                $tema->video = $request->file('video')->store('video'); // Se guarda la nueva
            }
            if ($request->modulo_id != -1) {
                $tema->modulo_id = $request->modulo_id;
            }
            $tema->save();

            return redirect("/temas/$tema->id/editar")->with('exito', "Tema $tema->nombre actualizado");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tema  $tema
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tema $tema)
    {
        $m_id = $tema->modulo_id;
        $tema->pruebas()->detach();
        
        foreach ($tema->preguntas as $p) {
            foreach ($p->respuestas as $r) {
                $r->delete();
            }
            $p->delete();
        }
        Storage::delete($tema->pdf);
        Storage::delete($tema->video);
        $tema->delete();

        return redirect("/modulos/$m_id/editar")->with('exito', 'Tema eliminado con éxito');
    }
}
