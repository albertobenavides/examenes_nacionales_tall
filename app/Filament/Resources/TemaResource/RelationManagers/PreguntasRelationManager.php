<?php

namespace App\Filament\Resources\TemaResource\RelationManagers;

use App\Models\Pregunta;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class PreguntasRelationManager extends RelationManager
{
    protected static string $relationship = 'preguntas';

    public function form(Form $form): Form
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
                    Repeater::make('respuestas')
                        ->relationship()
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
                        ])->columnSpanFull()
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('contenido')
            ->columns([
                Tables\Columns\TextColumn::make('contenido')->description(fn (Pregunta $record): string => $record->ayuda)->wrap()->searchable(),
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
