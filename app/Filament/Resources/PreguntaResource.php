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
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class PreguntaResource extends Resource
{
    protected static ?string $model = Pregunta::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TinyEditor::make('contenido')
                    ->profile('default')
                    ->setExternalPlugins([
                        'tiny_mce_wiris' => 'https://www.wiris.net/demo/plugins/tiny_mce/plugin.js',
                    ])
                    ->required()
                    ->columnSpanFull(),
                TinyEditor::make('ayuda')
                    ->profile('default')
                    ->setExternalPlugins([
                        'tiny_mce_wiris' => 'https://www.wiris.net/demo/plugins/tiny_mce/plugin.js',
                    ])
                    ->columnSpanFull(),
                Forms\Components\Select::make('curso_id')
                    ->relationship(name: 'curso', titleAttribute: 'nombre')
                    ->required(),
                Forms\Components\Select::make('tema_id')
                    ->relationship(name: 'tema', titleAttribute: 'nombre')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('contenido')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tema.nombre')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('curso.nombre')
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
