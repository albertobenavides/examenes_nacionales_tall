<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Curso;
use App\Models\Pago;
use App\Models\Promo;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Hash;
use Konnco\FilamentImport\Actions\ImportAction;
use Konnco\FilamentImport\Actions\ImportField;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ImportAction::make()
                ->fields([
                    ImportField::make('name')->label('Nombre')->required(),
                    ImportField::make('email')->label('Correo')->required(),
                    TextInput::make('password')->label('Contraseña alternativa'),
                    Select::make('curso')->options(Curso::all()->pluck('nombre', 'id')),
                    Select::make('promo')->options(Promo::all()->pluck('nombre', 'id')),
                    DatePicker::make('fin')->required()
                ])
                ->mutateBeforeCreate(function($row){
                    if ($row['password'] == null || $row['password'] == '') {
                        $row['password'] = Hash::make($row('alternative_password'));
                    }
                    $row['rol_id'] = 2;
                    return $row;
                })
                ->handleRecordCreation(function($data){
                    dd($data);
                    $u = User::create($data);
                    $pago = new Pago();
                    $pago->user_id = $u->id;
                    $pago->curso_id = $data['curso'];
                    $pago->promo_id = $data['promo'];
                    $pago->inicio = Carbon::today();
                    $pago->fin = $request->fin;
                    $pago->save();
                    return $u;
                })
        ];
    }
}
