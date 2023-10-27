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
                ->uniqueField('email')
                ->fields([
                    ImportField::make('name')->label('Nombre')->required(),
                    ImportField::make('email')->label('Correo')->required(),
                    TextInput::make('password')->label('Contraseña')->required(),
                    Select::make('curso')->options(Curso::where('activo', 1)->pluck('nombre', 'id')),
                    Select::make('promo')->options(Promo::all()->pluck('nombre', 'id')),
                    DatePicker::make('fin')->default(Carbon::now()->addMonths(2))->required()
                ])
                ->handleRecordCreation(function($data){
                    if ($data['name'] != null && $data['email'] != null && $data['name'] != '' && $data['email'] != '') {
                        $u = User::create([
                            'name' => $data['name'], 
                            'email' => $data['email'], 
                            'password' => $data['password'], 
                            'rol_id' => 2,
                            'por_admin' => 1
                        ]);
                        $u->assignRole('alumno');
    
                        $pago = new Pago();
                        $pago->user_id = $u->id;
                        $pago->curso_id = $data['curso'];
                        $pago->promo_id = $data['promo'];
                        $pago->inicio = Carbon::today();
                        $pago->fin = $data['fin'];
                        $pago->save();
                    }

                    return $u;
                })
        ];
    }
}
