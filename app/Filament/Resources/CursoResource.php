<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CursoResource\Pages;
use App\Filament\Resources\CursoResource\RelationManagers;
use App\Filament\Resources\CursoResource\RelationManagers\ModulosRelationManager;
use App\Models\Curso;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CursoResource extends Resource
{
    protected static ?string $model = Curso::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->required()
                            ->maxLength(191),
                        Forms\Components\Select::make('examen_id')
                            ->relationship('examen', 'nombre'),
                        Forms\Components\RichEditor::make('descripcion')
                            ->maxLength(191)
                            ->columnSpan(2),
                        Forms\Components\FileUpload::make('imagen')
                            ->image()
                            ->columnSpan(2),
                        Forms\Components\Toggle::make('activo')
                            ->required(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->description(fn (Curso $record): string => $record->descripcion ?? 'Falta descripción')
                    ->searchable(),
                Tables\Columns\TextColumn::make('examen.nombre'),
                Tables\Columns\ImageColumn::make('imagen')
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('activo'),
                Split::make([]),
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
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
            ModulosRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCursos::route('/'),
            'create' => Pages\CreateCurso::route('/create'),
            'edit' => Pages\EditCurso::route('/{record}/edit'),
        ];
    }
}
