<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Intento;
use App\Models\User;
use Illuminate\Http\Request;

class UserCursoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $intentos = Intento::where('user_id', auth()->id())->where('calificacion',  '>=', '90')
        ->get(['prueba_id', 'calificacion'])->flatten()
        ->sortByDesc('calificacion')->groupBy('prueba_id')->flatten()->unique('prueba_id')->count();
        return view('cursos.propios', [
            'intentos' => $intentos
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, User $user)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user, Curso $curso)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user, Curso $curso)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user, Curso $curso)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user, Curso $curso)
    {
        //
    }
}
