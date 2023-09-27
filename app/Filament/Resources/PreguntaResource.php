<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PreguntaResource\Pages;
use App\Filament\Resources\PreguntaResource\RelationManagers;
use App\Models\Pregunta;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PreguntaResource extends Resource
{
    protected static ?string $model = Pregunta::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('contenido')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('ayuda')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('tema_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('curso_id')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tema_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('curso_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPreguntas::route('/'),
            'create' => Pages\CreatePregunta::route('/create'),
            'edit' => Pages\EditPregunta::route('/{record}/edit'),
        ];
    }    
}
