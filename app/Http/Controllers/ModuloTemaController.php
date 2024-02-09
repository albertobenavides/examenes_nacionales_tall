<?php

namespace App\Http\Controllers;

use App\Models\Tema;
use App\Models\Modulo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModuloTemaController extends Controller
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
     * Display the specified resource.
     *
     * @param  \App\Models\Tema  $tema
     * @return \Illuminate\Http\Response
     */
    public function show(Modulo $modulo, Tema $tema)
    {
        return view('temas.mostrar', [
            'modulo' => $modulo,
            'tema' => $tema
        ]);
    }
}
