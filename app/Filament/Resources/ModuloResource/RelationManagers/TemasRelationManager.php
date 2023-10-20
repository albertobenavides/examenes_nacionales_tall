<?php

namespace App\Filament\Resources\ModuloResource\RelationManagers;

use App\Filament\Resources\TemaResource;
use App\Models\Tema;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
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
                    ->maxLength(191),
                Forms\Components\TextInput::make('descripcion')
                    ->maxLength(191),
                Forms\Components\FileUpload::make('imagen')
                    ->image(),
                Forms\Components\FileUpload::make('pdf'),
                Forms\Components\FileUpload::make('video'),
                Forms\Components\TextInput::make('preguntar')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nombre')
            ->columns([
                Tables\Columns\TextColumn::make('nombre'),
                Tables\Columns\TextColumn::make('orden'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('editar')->url(fn (Tema $record): string => TemaResource::getUrl('edit', ['record' => $record])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('orden')
            ->defaultSort('orden');
    }
}
