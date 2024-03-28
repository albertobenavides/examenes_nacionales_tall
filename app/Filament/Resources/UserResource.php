<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\Resources\UserResource\RelationManagers\PagosRelationManager;
use App\Models\Curso;
use App\Models\Intento;
use App\Models\Tema;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Spatie\Permission\Models\Role;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-s-users';

    protected static ?string $modelLabel = 'usuario';

    protected static ?string $navigationGroup = 'Administración';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->label('Nombre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')->label('Correo-e')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('password')->label('Contraseña')
                    ->password()
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->maxLength(255),
                Select::make('rol_id')->options(
                    auth()->user()->hasRole('consulta') ? Role::where('id', 2)->pluck('name', 'id') : Role::all()->pluck('name', 'id')
                )->default('2')->label('Rol'),
                Forms\Components\Repeater::make('pagos')
                    ->relationship()
                    ->schema([
                        Forms\Components\DatePicker::make('inicio')->default(Carbon::today())->columnSpan(2),
                        Forms\Components\DatePicker::make('fin')->default(Carbon::today()->addMonths(2))->columnSpan(2),
                        Select::make('promo_id')->relationship(name: 'promo', titleAttribute: 'nombre')->columnSpan(2),
                        Select::make('curso_id')->options(Curso::where('activo', 1)->pluck('nombre', 'id'))->label('Curso')->required()->columnSpan(2),
                    ])->columnSpanFull()
                    ->columns(4)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nombre')->searchable(),
                TextColumn::make('email')->label('Correo-e')->searchable()->copyable()->copyMessage('Color code copied'),
                TextColumn::make('rol_id')->label('Rol')->formatStateUsing(fn (string $state): string => Role::find(intval($state))->name),
                TextColumn::make('pagos.curso_id')->listWithLineBreaks()->formatStateUsing(fn (string $state): string => Curso::find(intval($state))->nombre),
                TextColumn::make('notes.avance')->state(function (User $record) {
                    if ($record->hasRole('alumno') && ($record->pagos->count() > 0)){
                        return $record->notes['avance'] ?? '';
                    } else {
                        return '';
                    }
                })->label('Avance'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('roles')->relationship('roles', 'name')
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Impersonate::make()->redirectTo('/learn'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            PagosRelationManager::class
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            // 'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }    
}
