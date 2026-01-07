<?php

namespace App\Helpers;

use App\Settings\ContactSettings;
use App\Settings\GeneralSettings;
use App\Settings\SocialMediaSettings;

class SettingsHelper
{
    /**
     * Get general settings instance
     */
    public static function general(): GeneralSettings
    {
        return app(GeneralSettings::class);
    }

    /**
     * Get contact settings instance
     */
    public static function contact(): ContactSettings
    {
        return app(ContactSettings::class);
    }

    /**
     * Get social media settings instance
     */
    public static function social(): SocialMediaSettings
    {
        return app(SocialMediaSettings::class);
    }

    /**
     * Get store name
     */
    public static function storeName(): string
    {
        return self::general()->store_name;
    }

    /**
     * Get store description
     */
    public static function storeDescription(): string
    {
        return self::general()->store_description;
    }

    /**
     * Get store logo URL
     */
    public static function storeLogo(): ?string
    {
        return self::general()->store_logo;
    }

    /**
     * Get store tagline
     */
    public static function storeTagline(): ?string
    {
        return self::general()->store_tagline;
    }

    /**
     * Get contact phone
     */
    public static function contactPhone(): string
    {
        return self::contact()->phone;
    }

    /**
     * Get contact email
     */
    public static function contactEmail(): string
    {
        return self::contact()->email;
    }

    /**
     * Get contact address
     */
    public static function contactAddress(): string
    {
        return self::contact()->address;
    }

    /**
     * Get contact city
     */
    public static function contactCity(): string
    {
        return self::contact()->city;
    }

    /**
     * Get contact country
     */
    public static function contactCountry(): string
    {
        return self::contact()->country;
    }

    /**
     * Get Facebook URL
     */
    public static function facebookUrl(): ?string
    {
        return self::social()->facebook_url;
    }

    /**
     * Get Twitter URL
     */
    public static function twitterUrl(): ?string
    {
        return self::social()->twitter_url;
    }

    /**
     * Get Instagram URL
     */
    public static function instagramUrl(): ?string
    {
        return self::social()->instagram_url;
    }

    /**
     * Get YouTube URL
     */
    public static function youtubeUrl(): ?string
    {
        return self::social()->youtube_url;
    }

    /**
     * Get LinkedIn URL
     */
    public static function linkedinUrl(): ?string
    {
        return self::social()->linkedin_url;
    }
}
