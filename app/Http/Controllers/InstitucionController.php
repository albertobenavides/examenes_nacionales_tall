<?php

namespace App\Http\Controllers;

use App\Models\Institucion;
use Illuminate\Http\Request;

class InstitucionController extends Controller
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
            'nombreInstitucion' => 'required',
        ]);

        $institucion = new Institucion;
        $institucion->nombre = $request->nombreInstitucion;
        $institucion->siglas = $request->siglasInstitucion;
        $institucion->estado = $request->estadoInstitucion;
        $institucion->pais = $request->paisInstitucion;
        if(isset($request->examenInstitucion)){
            $institucion->examen_id = $request->examenInstitucion;
        }
        $institucion->save();

        return back()->with('exito', 'Institucion ' . $institucion->nombre . ' creado');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Institucion  $institucion
     * @return \Illuminate\Http\Response
     */
    public function show(Institucion $institucion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Institucion  $institucion
     * @return \Illuminate\Http\Response
     */
    public function edit(Institucion $institucion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Institucion  $institucion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombreInstitucion' => 'required',
        ]);

        $institucion = Institucion::find($id);
        $institucion->nombre = $request->nombreInstitucion;
        $institucion->siglas = $request->siglasInstitucion;
        $institucion->estado = $request->estadoInstitucion;
        $institucion->pais = $request->paisInstitucion;
        if(isset($request->examenInstitucion)){
            $institucion->examen_id = $request->examenInstitucion;
        }
        $institucion->save();

        return back()->with('exito', 'Institucion ' . $institucion->nombre . ' actualizada');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Institucion  $institucion
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $institucion = Institucion::find($id);

        $institucion->delete();

        return back()->with('exito', 'Institucion eliminada.');
    }
}
