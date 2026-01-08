<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ContactSettings extends Settings
{
    public string $phone;

    public string $email;

    public string $address;

    public string $city;

    public string $country;

    public static function group(): string
    {
        return 'contact';
    }
}
