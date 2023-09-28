<?php

namespace App\Filament\Resources\IntentoResource\RelationManagers;

use App\Models\Pregunta;
use App\Models\Respuesta;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PreguntasRelationManager extends RelationManager
{
    protected static string $relationship = 'preguntas';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('contenido')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('contenido')
            ->columns([
                Tables\Columns\TextColumn::make('contenido'),
                Tables\Columns\TextColumn::make('respuestas')->label('Respuesta')->formatStateUsing(function ($record) {
                    return Respuesta::whereIn('id', $record->respuestas)->whereIn('id', $this->getOwnerRecord()->respuestas)->first()->contenido;
                }),
                Tables\Columns\TextColumn::make('respuestas')->label('correcta')->formatStateUsing(function ($record) {
                    return Respuesta::whereIn('id', $record->respuestas)->whereIn('id', $this->getOwnerRecord()->respuestas)->first()->correcta;
                }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
