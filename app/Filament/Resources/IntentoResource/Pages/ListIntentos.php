<?php

namespace App\Filament\Resources\IntentoResource\Pages;

use App\Filament\Resources\IntentoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIntentos extends ListRecords
{
    protected static string $resource = IntentoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
