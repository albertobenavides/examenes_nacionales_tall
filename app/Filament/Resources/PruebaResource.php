<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PruebaResource\Pages;
use App\Filament\Resources\PruebaResource\RelationManagers;
use App\Filament\Resources\PruebaResource\RelationManagers\TemasRelationManager;
use App\Models\Modulo;
use App\Models\Prueba;
use App\Models\Tema;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class PruebaResource extends Resource
{
    protected static ?string $model = Prueba::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(191),
                Forms\Components\RichEditor::make('descripcion')
                    ->columnSpanFull(),
                Forms\Components\Select::make('curso_id')
                    ->relationship(name: 'curso', titleAttribute: 'nombre')
                    ->required(),
                // Repeater::make('modulos')
                //     ->relationship()
                //     ->schema([
                //         Placeholder::make('nombre')->content(function (Modulo $record) {return new HtmlString("<h1 style='font-size:30px'>$record->nombre</h1>");}),
                //         Repeater::make('temas')
                //             ->relationship()
                //             ->schema([
                //                 Placeholder::make('nombre')->content(fn (Tema $record): string => $record->nombre)->columnSpan(4),
                //                 TextInput::make('preguntas')->numeric()->columnSpan(2)
                //             ])->addable(false)->deletable(false)
                //     ])->addable(false)->deletable(false)
                //     ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
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
            TemasRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPruebas::route('/'),
            'create' => Pages\CreatePrueba::route('/create'),
            'edit' => Pages\EditPrueba::route('/{record}/edit'),
        ];
    }
}
