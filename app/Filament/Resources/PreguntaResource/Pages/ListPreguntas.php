<?php

namespace App\Filament\Resources\PreguntaResource\Pages;

use App\Filament\Resources\PreguntaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPreguntas extends ListRecords
{
    protected static string $resource = PreguntaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
