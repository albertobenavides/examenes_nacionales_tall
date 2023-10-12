<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.app_name', '');
        $this->migrator->add('general.portada1', '');
        $this->migrator->add('general.portada2', '');
        $this->migrator->add('general.portada3', '');
        $this->migrator->add('general.stripe_pk', '');
        $this->migrator->add('general.stripe_sk', '');
        $this->migrator->add('general.conekta_pk', '');
        $this->migrator->add('general.conekta_sk', '');

    }
};
