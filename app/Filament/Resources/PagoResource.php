<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PagoResource\Pages;
use App\Filament\Resources\PagoResource\RelationManagers;
use App\Models\Pago;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PagoResource extends Resource
{
    protected static ?string $model = Pago::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('inicio'),
                Forms\Components\DatePicker::make('fin'),
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('promo_id')
                    ->numeric(),
                Forms\Components\TextInput::make('curso_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('oxxo')
                    ->maxLength(191),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('inicio')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fin')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('usuario.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('promo.nombre')
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
                Tables\Columns\TextColumn::make('oxxo')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListPagos::route('/'),
            'create' => Pages\CreatePago::route('/create'),
            // 'edit' => Pages\EditPago::route('/{record}/edit'),
        ];
    }    
}
