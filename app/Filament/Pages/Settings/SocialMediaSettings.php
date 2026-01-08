<?php

namespace App\Filament\Pages\Settings;

use App\Enums\NavigationGroup;
use App\Settings\SocialMediaSettings as SocialMediaSettingsModel;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class SocialMediaSettings extends SettingsPage
{
    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedShare;

    protected static string $settings = SocialMediaSettingsModel::class;

    protected static ?string $navigationLabel = null;

    protected static ?string $title = null;

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('filament.settings.social_media_settings.navigation_label');
    }

    public function getTitle(): string
    {
        return __('filament.settings.social_media_settings.title');
    }

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Settings;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.settings.social_media_settings.social_links_section'))
                    ->description(__('filament.settings.social_media_settings.social_links_description'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('facebook_url')
                                    ->label(__('filament.settings.social_media_settings.facebook_url'))
                                    ->url()
                                    ->maxLength(500)
                                    ->placeholder('https://facebook.com/yourpage')
                                    ->columnSpan(1),

                                TextInput::make('twitter_url')
                                    ->label(__('filament.settings.social_media_settings.twitter_url'))
                                    ->url()
                                    ->maxLength(500)
                                    ->placeholder('https://twitter.com/yourhandle')
                                    ->columnSpan(1),

                                TextInput::make('instagram_url')
                                    ->label(__('filament.settings.social_media_settings.instagram_url'))
                                    ->url()
                                    ->maxLength(500)
                                    ->placeholder('https://instagram.com/yourhandle')
                                    ->columnSpan(1),

                                TextInput::make('youtube_url')
                                    ->label(__('filament.settings.social_media_settings.youtube_url'))
                                    ->url()
                                    ->maxLength(500)
                                    ->placeholder('https://youtube.com/@yourchannel')
                                    ->columnSpan(1),

                                TextInput::make('linkedin_url')
                                    ->label(__('filament.settings.social_media_settings.linkedin_url'))
                                    ->url()
                                    ->maxLength(500)
                                    ->placeholder('https://linkedin.com/company/yourcompany')
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }
}
