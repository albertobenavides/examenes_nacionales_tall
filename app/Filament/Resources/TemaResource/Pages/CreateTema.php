<?php

namespace App\Filament\Resources\TemaResource\Pages;

use App\Filament\Resources\TemaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateTema extends CreateRecord
{
    protected static string $resource = TemaResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        if ($data['video'] == null) {
            $data['video'] = $data['video_file'];
        }
        unset($data['video_file']);
        
        return static::getModel()::create($data);
    }
}
