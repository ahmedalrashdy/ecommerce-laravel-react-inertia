<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('social_media.facebook_url', null);
        $this->migrator->add('social_media.twitter_url', null);
        $this->migrator->add('social_media.instagram_url', null);
        $this->migrator->add('social_media.youtube_url', null);
        $this->migrator->add('social_media.linkedin_url', null);
    }
};
