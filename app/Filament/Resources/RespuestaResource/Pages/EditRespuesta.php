<?php

namespace App\Filament\Resources\RespuestaResource\Pages;

use App\Filament\Resources\RespuestaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRespuesta extends EditRecord
{
    protected static string $resource = RespuestaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
