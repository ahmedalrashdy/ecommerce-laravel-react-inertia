<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('contact.phone', '+966 12 345 6789');
        $this->migrator->add('contact.email', 'info@store.com');
        $this->migrator->add('contact.address', 'الرياض، المملكة العربية السعودية');
        $this->migrator->add('contact.city', 'الرياض');
        $this->migrator->add('contact.country', 'السعودية');
    }
};
