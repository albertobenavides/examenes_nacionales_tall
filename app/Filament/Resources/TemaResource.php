<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TemaResource\Pages;
use App\Filament\Resources\TemaResource\RelationManagers;
use App\Models\Tema;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TemaResource extends Resource
{
    protected static ?string $model = Tema::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('descripcion')
                    ->maxLength(191),
                Forms\Components\TextInput::make('imagen')
                    ->maxLength(191),
                Forms\Components\TextInput::make('pdf')
                    ->maxLength(191),
                Forms\Components\TextInput::make('video')
                    ->maxLength(191),
                Forms\Components\TextInput::make('orden')
                    ->numeric(),
                Forms\Components\TextInput::make('modulo_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('preguntar')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('descripcion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('imagen')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pdf')
                    ->searchable(),
                Tables\Columns\TextColumn::make('video')
                    ->searchable(),
                Tables\Columns\TextColumn::make('orden')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('modulo_id')
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
                Tables\Columns\TextColumn::make('preguntar')
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
            'index' => Pages\ListTemas::route('/'),
            'create' => Pages\CreateTema::route('/create'),
            'edit' => Pages\EditTema::route('/{record}/edit'),
        ];
    }    
}