<?php

namespace App\Filament\Resources\ExamenResource\Pages;

use App\Filament\Resources\ExamenResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExamen extends EditRecord
{
    protected static string $resource = ExamenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
