<?php

namespace App\Filament\Pages\Settings;

use App\Enums\NavigationGroup;
use App\Settings\ContactSettings as ContactSettingsModel;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ContactSettings extends SettingsPage
{
    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedPhone;

    protected static string $settings = ContactSettingsModel::class;

    protected static ?string $navigationLabel = null;

    protected static ?string $title = null;

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('filament.settings.contact_settings.navigation_label');
    }

    public function getTitle(): string
    {
        return __('filament.settings.contact_settings.title');
    }

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Settings;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.settings.contact_settings.contact_info_section'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('phone')
                                    ->label(__('filament.settings.contact_settings.phone'))
                                    ->required()
                                    ->tel()
                                    ->maxLength(255)
                                    ->columnSpan(1),

                                TextInput::make('email')
                                    ->label(__('filament.settings.contact_settings.email'))
                                    ->required()
                                    ->email()
                                    ->maxLength(255)
                                    ->columnSpan(1),

                                TextInput::make('address')
                                    ->label(__('filament.settings.contact_settings.address'))
                                    ->required()
                                    ->maxLength(500)
                                    ->columnSpanFull(),

                                TextInput::make('city')
                                    ->label(__('filament.settings.contact_settings.city'))
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(1),

                                TextInput::make('country')
                                    ->label(__('filament.settings.contact_settings.country'))
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(1),
                            ]),
                    ]),
            ]);
    }
}
