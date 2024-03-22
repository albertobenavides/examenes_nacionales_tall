<?php

namespace App\Filament\Learn\Pages;

use App\Models\Intento;
use Filament\Pages\Page;

class Cursos extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.learn.pages.cursos';

    public $intentos;
 
    public function mount() 
    {
        $this->intentos = Intento::where('user_id', auth()->id())->where('calificacion',  '>=', '90')
        ->get(['prueba_id', 'calificacion'])->flatten()
        ->sortByDesc('calificacion')->groupBy('prueba_id')->flatten()->unique('prueba_id')->count();
    }
}
