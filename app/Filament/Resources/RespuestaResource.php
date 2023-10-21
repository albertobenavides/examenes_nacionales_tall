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
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class RespuestaResource extends Resource
{
    protected static ?string $model = Respuesta::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

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
                Forms\Components\Toggle::make('correcta')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pregunta.contenido')
                    ->wrap()
                    ->placeholder('Pregunta borrada.')
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('correcta'),
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
