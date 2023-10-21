<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class General extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = GeneralSettings::class;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole(['super_admin']);
    }

    public function mount(): void
    {
        abort_unless(auth()->user()->hasRole(['super_admin']), 403);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('app_name')->required(),
                FileUpload::make('portada1')->image()->required(),
                FileUpload::make('portada2')->image()->required(),
                FileUpload::make('portada3')->image()->required(),
                TextInput::make('stripe_pk')->required(),
                TextInput::make('stripe_sk')->required(),
                TextInput::make('conekta_pk')->required(),
                TextInput::make('conekta_sk')->required(),
            ]);
    }
}
