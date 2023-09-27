<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IntentoResource\Pages;
use App\Filament\Resources\IntentoResource\RelationManagers;
use App\Models\Intento;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class IntentoResource extends Resource
{
    protected static ?string $model = Intento::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('prueba_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('calificacion')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('aciertos')
                    ->numeric(),
                Forms\Components\Textarea::make('preguntas')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('respuestas')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('prueba_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('calificacion')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('aciertos')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListIntentos::route('/'),
            'create' => Pages\CreateIntento::route('/create'),
            'edit' => Pages\EditIntento::route('/{record}/edit'),
        ];
    }    
}
