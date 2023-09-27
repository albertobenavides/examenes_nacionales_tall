<?php

namespace App\Filament\Resources\PruebaResource\Pages;

use App\Filament\Resources\PruebaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPruebas extends ListRecords
{
    protected static string $resource = PruebaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
