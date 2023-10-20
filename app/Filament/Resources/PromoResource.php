<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromoResource\Pages;
use App\Filament\Resources\PromoResource\RelationManagers;
use App\Models\Promo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PromoResource extends Resource
{
    protected static ?string $model = Promo::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Administración';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(191),
                Forms\Components\Select::make('curso_id')
                    ->relationship(name: 'curso', titleAttribute: 'nombre')    
                    ->required(),
                Forms\Components\TextInput::make('costo')
                    ->required()
                    ->numeric()
                    ->minValue(0),
                Forms\Components\Toggle::make('videos')
                    ->required(),
                Forms\Components\TextInput::make('examenes')
                    ->required()
                    ->numeric(),
                Forms\Components\Toggle::make('duracion')
                    ->required(),
                Forms\Components\RichEditor::make('descripcion')
                    ->maxLength(191),
                Forms\Components\RichEditor::make('contenido')
                    ->maxLength(191),
                Forms\Components\FileUpload::make('imagen')
                    ->image(),
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
                Tables\Columns\TextColumn::make('costo')
                    ->numeric()
                    ->formatStateUsing(fn (string $state): string => "$" . number_format($state, 2))
                    ->sortable()
                    ->alignEnd(),
                Tables\Columns\IconColumn::make('videos')
                    ->boolean(),
                Tables\Columns\TextColumn::make('examenes')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('duracion')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('descripcion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contenido')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('imagen')
                    ->searchable(),
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
            'index' => Pages\ListPromos::route('/'),
            'create' => Pages\CreatePromo::route('/create'),
            'edit' => Pages\EditPromo::route('/{record}/edit'),
        ];
    }    
}
