<?php

namespace App\Filament\Resources\InstitucionResource\Pages;

use App\Filament\Resources\InstitucionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewInstitucion extends ViewRecord
{
    protected static string $resource = InstitucionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
