<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InstitucionResource\Pages;
use App\Filament\Resources\InstitucionResource\RelationManagers;
use App\Models\Examen;
use App\Models\Institucion;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InstitucionResource extends Resource
{
    protected static ?string $model = Institucion::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $pluralModelLabel = 'instituciones';

    protected static ?string $navigationGroup = 'Educación';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('siglas')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('pais')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('estado')
                    ->required()
                    ->maxLength(191),
                Forms\Components\Select::make('examen_id')
                    ->relationship('curso', 'nombre'),
                FileUpload::make('imagen')->image(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('siglas')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('imagen'),
                Tables\Columns\TextColumn::make('pais')
                    ->searchable(),
                Tables\Columns\TextColumn::make('estado')
                    ->searchable(),
                Tables\Columns\TextColumn::make('examen.nombre'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInstitucions::route('/'),
            'create' => Pages\CreateInstitucion::route('/create'),
            'edit' => Pages\EditInstitucion::route('/{record}/edit'),
        ];
    }
}
