<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\Curso;
use App\Models\Intento;
use App\Models\Pago;
use App\Models\Tema;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PagosRelationManager extends RelationManager
{
    protected static string $relationship = 'pagos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('inicio')->default(Carbon::now()),
                Forms\Components\DatePicker::make('fin')->default(Carbon::now()->addMonths(2)),
                Select::make('promo_id')->relationship(name: 'promo', titleAttribute: 'nombre'),
                Select::make('curso_id')->options(Curso::where('activo', 1)->pluck('nombre', 'id'))->label('Curso')->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('curso_id')
            ->columns([
                Tables\Columns\TextColumn::make('inicio'),
                Tables\Columns\TextColumn::make('fin'),
                Tables\Columns\TextColumn::make('curso.nombre'),
                Tables\Columns\TextColumn::make('promo.nombre'),
                Tables\Columns\TextColumn::make('oxxo'),
                TextColumn::make('avance')->state(function (Pago $record) {
                    $intentos = Intento::where('user_id', $record->user_id)->where('calificacion',  '>=', '90')->get(['prueba_id', 'calificacion'])->flatten()->sortByDesc('calificacion')->groupBy('prueba_id')->flatten()->unique('prueba_id')->count();
                    $temas = Tema::whereIn('modulo_id', $record->curso->modulos->pluck('id'))->where('preguntar', '>', 0)->count();
                    return $temas > 0 ? round(($intentos / $temas * 100), 2) . '%' : '0%';
                })
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
