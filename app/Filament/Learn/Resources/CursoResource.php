<?php

namespace App\Filament\Learn\Resources;

use App\Filament\Learn\Resources\CursoResource\Pages;
use App\Filament\Learn\Resources\CursoResource\RelationManagers;
use App\Models\Curso;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CursoResource extends Resource
{
    protected static ?string $model = Curso::class;

    protected static ?string $modelLabel = 'Mis cursos';

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getBreadcrumb(): string
{
    return '';
}

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCursos::route('/'),
            'view' => Pages\ViewCurso::route('/{record}'),
            'temas' => Pages\ViewTema::route('/{record}/modulos/{modulo}/temas/{tema}'),
            'ejercicios' => Pages\ViewEjercicios::route('/{record}/modulos/{modulo}/temas/{tema}/ejercicios'),
            'examen' => Pages\ViewExamen::route('/{record}/examenes/{id}'),
        ];
    }
}
