<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $store_name;

    public string $store_description;

    public ?string $store_logo;

    public ?string $store_favicon;

    public ?string $store_tagline;

    public static function group(): string
    {
        return 'general';
    }
}
