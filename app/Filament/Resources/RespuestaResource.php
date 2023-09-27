<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RespuestaResource\Pages;
use App\Filament\Resources\RespuestaResource\RelationManagers;
use App\Models\Respuesta;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RespuestaResource extends Resource
{
    protected static ?string $model = Respuesta::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('contenido')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('pregunta_id')
                    ->required()
                    ->numeric(),
                Forms\Components\Toggle::make('correcta')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pregunta_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('correcta')
                    ->boolean(),
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
            'index' => Pages\ListRespuestas::route('/'),
            'create' => Pages\CreateRespuesta::route('/create'),
            'edit' => Pages\EditRespuesta::route('/{record}/edit'),
        ];
    }    
}
