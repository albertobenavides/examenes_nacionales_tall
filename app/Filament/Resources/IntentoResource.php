<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IntentoResource\Pages;
use App\Filament\Resources\IntentoResource\RelationManagers;
use App\Filament\Resources\IntentoResource\RelationManagers\PreguntasRelationManager;
use App\Models\Intento;
use App\Models\Prueba;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Livewire;

class IntentoResource extends Resource
{
    protected static ?string $model = Intento::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('usuario', 'name')
                    ->required(),
                Forms\Components\TextInput::make('prueba_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('calificacion')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('aciertos')
                    ->numeric(),
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
                Tables\Columns\TextColumn::make('usuario.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('prueba_id')
                    ->formatStateUsing(fn (string $state): string => Prueba::find(abs($state))->nombre ?? 'Prueba borrada'),
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
            PreguntasRelationManager::class
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
