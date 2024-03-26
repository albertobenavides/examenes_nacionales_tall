<?php

namespace App\Filament\Learn\Resources\CursoResource\Pages;

use App\Filament\Learn\Resources\CursoResource;
use App\Models\Modulo;
use App\Models\Tema;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;

class ViewEjercicios extends Page
{
    use InteractsWithRecord;
    
    protected static string $resource = CursoResource::class;

    protected static string $view = 'filament.learn.resources.curso-resource.pages.view-ejercicios';

    public $modulo, $tema;

    public function mount(int | string $record, $modulo, $tema): void
    {
        $this->record = $this->resolveRecord($record);
        $this->modulo = Modulo::find($modulo);
        $this->tema = Tema::find($tema);
    }
}
