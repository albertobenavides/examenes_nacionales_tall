<?php

namespace App\Filament\Learn\Resources\CursoResource\Pages;

use App\Filament\Learn\Resources\CursoResource;
use App\Models\Intento;
use Filament\Resources\Pages\Page;

class ListCursos extends Page
{
    protected static string $resource = CursoResource::class;

    protected static string $view = 'filament.learn.resources.curso-resource.pages.list';

    public $intentos;
 
    public function mount() 
    {
        $this->intentos = Intento::where('user_id', auth()->id())->where('calificacion',  '>=', '90')
        ->get(['prueba_id', 'calificacion'])->flatten()
        ->sortByDesc('calificacion')->groupBy('prueba_id')->flatten()->unique('prueba_id')->count();
    }
}
