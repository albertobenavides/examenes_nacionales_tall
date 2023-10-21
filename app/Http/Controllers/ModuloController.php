<?php

namespace App\Http\Controllers;

use App\Models\Intento;
use App\Models\Modulo;
use App\Models\Pago;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ModuloController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('revisar.rol:1')->except(['show']);
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
            'curso_id' => 'required'
        ]);

        $modulo = new Modulo;
        $modulo->nombre = $request->nombre;
        $modulo->descripcion = $request->descripcion;
        if ($request->imagen) { // Si se especifica una imagen de avatar
            $modulo->imagen = $request->file('imagen')->store('modulos', 'public'); // Se guarda la nueva
        }
        $modulo->curso_id = $request->curso_id;
        $modulo->save();

        foreach ($modulo->curso->pruebas as $p) {
            $modulo->pruebas()->syncWithoutDetaching($p->id);
        }

        return back()->with('exito', 'Modulo ' . $modulo->nombre . ' creado');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Modulo  $modulo
     * @return \Illuminate\Http\Response
     */
    public function show(Modulo $modulo)
    {
        $pago = Pago::where('user_id', Auth::id())->where('curso_id', $modulo->curso_id)->where('fin', '>=', Carbon::today())->orderByDesc('promo_id')->first();
        $temas = $modulo->temas->sortBy('orden');
        $totales = 0.0;
        $temas->where('preguntar', '>', 0)->map(function($t){
            $t['max'] = Intento::where('user_id', Auth::id())->where('prueba_id', $t->id * -1)->where('calificacion', '>', -1)->max('calificacion');
            return $t;
        });
        $temas->where('preguntar', '>', 0)->count();
        $temas->where('preguntar', '>', 0);
        $pasados = Intento::where('user_id', Auth::id())->whereIn('prueba_id', array_map(function($el) { return $el * -1; }, $temas->pluck('id')->all()))->where('calificacion', '>', -1)->max('calificacion');
        return view('modulos.mostrar', [
            'modulo' => $modulo,
            'pago' => $pago,
            'temas' => $temas,
            'totales' => $totales,
            'pasados' => $pasados
        ]);
        return view('modulos.mostrar',[
            'modulo' => $modulo
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Modulo  $modulo
     * @return \Illuminate\Http\Response
     */
    public function edit(Modulo $modulo)
    {
        return view('modulos.editar', [
            'modulo' => $modulo
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Modulo  $modulo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Modulo $modulo)
    {
        if(isset($request->modulos)){
            for ($i=0; $i < count($request->modulos); $i++) {
                $modulo = Modulo::find($request->modulos[$i]);
                $modulo->orden = $i;
                $modulo->save();
            }
        } else{
            $request->validate([
                'nombre' => 'required',
                'curso_id' => 'required'
            ]);

            $modulo->nombre = $request->nombre;
            if ($request->imagen) {
                if($modulo->imagen != null){
                    Storage::disk('public')->delete($modulo->imagen);
                }
                $modulo->imagen = $request->file('imagen')->store('modulos', 'public');
            } elseif(isset($request->eliminarImagen)){
                Storage::disk('public')->delete($modulo->imagen);
                $modulo->imagen = null;
            }
            if($request->curso_id != -1){
                $modulo->curso_id = $request->curso_id;
            }
            $modulo->save();

            return redirect("/modulos/$modulo->id/editar")->with('exito', "Modulo $modulo->nombre actualizado");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Modulo  $modulo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Modulo $modulo)
    {
        $modulo->pruebas()->detach();

        $c = $modulo->curso->id;
        foreach ($modulo->temas as $t) {
            foreach ($t->preguntas as $p) {
                foreach ($p->respuestas as $r) {
                    $r->delete();
                }
                $p->delete();
            }
            Storage::delete($t->pdf);
            Storage::delete($t->video);
            $t->delete();
        }
        $modulo->delete();

        return redirect("/cursos/$c/editar")->with('exito', 'Módulo eliminado.');
    }
}
