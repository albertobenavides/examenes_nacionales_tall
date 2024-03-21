<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TemaResource\Pages;
use App\Filament\Resources\TemaResource\RelationManagers;
use App\Filament\Resources\TemaResource\RelationManagers\PreguntasRelationManager;
use App\Models\Tema;
use Filament\Forms;
use Filament\Forms\Components\Builder as ComponentsBuilder;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class TemaResource extends Resource
{
    protected static ?string $model = Tema::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nombre')->required()->maxLength(191),
                TextInput::make('descripcion')->maxLength(191),
                ComponentsBuilder::make('contenido')->columnSpanFull()
                    ->blocks([
                        ComponentsBuilder\Block::make('texto')
                            ->schema([
                                TextInput::make('titulo')->label('Título'),
                                TinyEditor::make('texto')
                                    ->profile('default')
                                    ->setExternalPlugins([
                                        'tiny_mce_wiris' => 'https://www.wiris.net/demo/plugins/tiny_mce/plugin.js',
                                    ])
                                    ->columnSpanFull(),
                            ])
                            ->columns(1),
                        ComponentsBuilder\Block::make('embebido')
                            ->schema([
                                TextInput::make('titulo')->label('Título'),
                                Textarea::make('embebido')
                                    ->required(),
                            ])
                            ->columns(1),
                        ComponentsBuilder\Block::make('pdf')
                            ->schema([
                                TextInput::make('titulo')->label('Título'),
                                FileUpload::make('url')
                                    ->label('PDF')
                                    ->image()
                                    ->required()
                                    ->acceptedFileTypes(['application/pdf']),
                            ]),
                        ComponentsBuilder\Block::make('video')
                            ->schema([
                                TextInput::make('titulo')->label('Título'),
                                FileUpload::make('video')
                                    ->required(),
                            ]),
                        ComponentsBuilder\Block::make('h5p')
                            ->schema([
                                TextInput::make('titulo')->label('Título'),
                                FileUpload::make('h5p')->required(),
                            ]),
                    ]),
                // Select::make('modulo_id')->relationship(name: 'modulo', titleAttribute: 'nombre')->searchable(['nombre'])->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->description(fn (Tema $record): string => $record->description ?? '')
                    ->searchable(),
                Tables\Columns\TextColumn::make('orden')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('modulo.nombre')
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
                    BulkAction::make('Actualizar')->visible(auth()->user()->hasRole('super_admin'))
                        ->action(function ($records) {
                            foreach ($records as $tema) {
                                $contenido = [];
                                if ($tema->pdf != null) {
                                    $contenido[] = ["data" => ["url" => $tema->pdf], "type" => "pdf"];
                                }
                                if ($tema->video != null) {
                                    if (str_contains($tema->video, 'http')) {
                                        $contenido[] = ["data" => ["embebido" => $tema->video], "type" => "embebido"];
                                    }
                                    if (!str_contains($tema->video, 'http')) {
                                        $contenido[] = ["data" => ["video" => $tema->video], "type" => "video"];
                                    }
                                }
                                $tema->contenido = $contenido;
                                $tema->save();
                            }
                        })
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PreguntasRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTemas::route('/'),
            'create' => Pages\CreateTema::route('/create'),
            'edit' => Pages\EditTema::route('/{record}/edit'),
        ];
    }
}
