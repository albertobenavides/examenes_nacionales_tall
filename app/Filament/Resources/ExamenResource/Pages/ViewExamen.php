<?php

namespace App\Filament\Resources\ExamenResource\Pages;

use App\Filament\Resources\ExamenResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewExamen extends ViewRecord
{
    protected static string $resource = ExamenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
