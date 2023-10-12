<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{

    public string $app_name;
    public string $portada1;
    public string $portada2;
    public string $portada3;
    public string $stripe_pk;
    public string $stripe_sk;
    public string $conekta_pk;
    public string $conekta_sk;

    public static function group(): string
    {
        return 'general';
    }
}