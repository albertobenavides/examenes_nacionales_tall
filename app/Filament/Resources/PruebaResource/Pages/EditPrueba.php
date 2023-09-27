<?php

namespace App\Filament\Resources\PruebaResource\Pages;

use App\Filament\Resources\PruebaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPrueba extends EditRecord
{
    protected static string $resource = PruebaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
