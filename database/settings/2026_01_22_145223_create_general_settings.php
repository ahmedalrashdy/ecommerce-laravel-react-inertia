<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.store_name', 'متجري');
        $this->migrator->add('general.store_description', 'وجهتك الأولى للتسوق الإلكتروني. نوفر لك أفضل المنتجات بأسعار تنافسية وخدمة عملاء متميزة.');
        $this->migrator->add('general.store_logo', null);
        $this->migrator->add('general.store_favicon', null);
        $this->migrator->add('general.store_tagline', 'أفضل الأسعار دائماً');
    }
};
