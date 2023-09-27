<?php

namespace App\Filament\Resources\TemaResource\Pages;

use App\Filament\Resources\TemaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTemas extends ListRecords
{
    protected static string $resource = TemaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
