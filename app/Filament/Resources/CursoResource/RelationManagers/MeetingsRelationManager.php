<?php

namespace App\Filament\Resources\CursoResource\RelationManagers;

use App\Models\Meeting;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MeetingsRelationManager extends RelationManager
{
    protected static string $relationship = 'meetings';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('meetingName')->required()->label('Nombre'),
                DateTimePicker::make('inicio')->default(Carbon::now()->addMinutes(5)->startOfMinute())->required(),
                DateTimePicker::make('fin')->default(Carbon::now()->addMinutes(5)->addHours()->startOfMinute()),
                
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('meetingName')
            ->columns([
                TextColumn::make('meetingName')->label('Nombre'),
                TextColumn::make('inicio'),
                TextColumn::make('fin'),
                TextColumn::make('status')->label('Estado'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Action::make('Moderar')
                    ->action(function (Meeting $record) {
                        return redirect()->to(
                            \Bigbluebutton::join([
                                'meetingID' => $record->id,
                                'userName' => auth()->user()->name,
                                'role' => 'MODERATOR',
                                'userId' =>  auth()->id(),
                            ])
                        );
                    })->visible(fn ($record) => $record->status == null),
                Action::make('Unirse')
                    ->action(function (Meeting $record) {
                        return redirect()->to(
                            \Bigbluebutton::join([
                                'meetingID' => $record->id,
                                'userName' => auth()->user()->name,
                                'role' => 'VIEWER',
                                'guest' => true,
                                'userId' =>  auth()->id(),
                            ])
                        );
                    })->visible(fn ($record) => $record->status == null),
                Action::make('Grabación')
                    ->action(function (Meeting $record) {
                        try {
                            $video = \Bigbluebutton::getRecordings(['meetingID' => $record->id])[0]['playback']['format']['url'];
                            return redirect()->to($video);
                        } catch (\Throwable $th) {
                            Notification::make()
                                ->title('Video no disponible')
                                ->warning()
                                ->send();
                        }
                    })->visible(fn ($record) => $record->status != null),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
