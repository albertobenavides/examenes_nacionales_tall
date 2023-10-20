<?php

namespace App\Filament\Resources\PruebaResource\RelationManagers;

use App\Filament\Resources\TemaResource;
use App\Models\Tema;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TemasRelationManager extends RelationManager
{
    protected static string $relationship = 'temas';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nombre')
            ->columns([
                TextColumn::make('nombre')->searchable()->url(fn (Tema $record): string => TemaResource::getUrl('edit', ['record' => $record])),
                TextInputColumn::make('preguntas')->rules(['required', 'numeric', 'min:0'])->type('number'),
            ])
            ->groups([
                Group::make('modulo.nombre')->collapsible(),
                ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
