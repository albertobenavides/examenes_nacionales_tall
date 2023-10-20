<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
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
                Forms\Components\DatePicker::make('inicio'),
                Forms\Components\DatePicker::make('fin'),
                Select::make('promo_id')->relationship(name: 'promo', titleAttribute: 'nombre'),
                Select::make('curso_id')->relationship(name: 'curso', titleAttribute: 'nombre')->required(),
                Forms\Components\TextInput::make('oxxo')
                    ->maxLength(191),
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
