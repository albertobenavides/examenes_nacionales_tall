<?php

namespace App\Filament\Learn\Resources\CursoResource\Pages;

use App\Filament\Learn\Resources\CursoResource;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;

class ViewCurso extends Page
{
    use InteractsWithRecord;
    
    protected static string $resource = CursoResource::class;

    protected static string $view = 'filament.learn.resources.curso-resource.pages.view';

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }
}
