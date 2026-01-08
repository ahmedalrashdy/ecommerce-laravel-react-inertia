<?php

namespace App\Filament\Pages\Settings;

use App\Enums\NavigationGroup;
use App\Settings\GeneralSettings as GeneralSettingsModel;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class GeneralSettings extends SettingsPage
{
    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string $settings = GeneralSettingsModel::class;

    protected static ?string $navigationLabel = null;

    protected static ?string $title = null;

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('filament.settings.general_settings.navigation_label');
    }

    public function getTitle(): string
    {
        return __('filament.settings.general_settings.title');
    }

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Settings;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.settings.general_settings.store_info_section'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('store_name')
                                    ->label(__('filament.settings.general_settings.store_name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                Textarea::make('store_description')
                                    ->label(__('filament.settings.general_settings.store_description'))
                                    ->required()
                                    ->rows(3)
                                    ->columnSpanFull(),

                                TextInput::make('store_tagline')
                                    ->label(__('filament.settings.general_settings.store_tagline'))
                                    ->maxLength(255)
                                    ->placeholder(__('filament.settings.general_settings.store_tagline_placeholder'))
                                    ->columnSpanFull(),

                                FileUpload::make('store_logo')
                                    ->label(__('filament.settings.general_settings.store_logo'))
                                    ->image()
                                    ->directory('settings')
                                    ->columnSpan(1),

                                FileUpload::make('store_favicon')
                                    ->label(__('filament.settings.general_settings.store_favicon'))
                                    ->image()
                                    ->directory('settings')
                                    ->acceptedFileTypes(['image/png', 'image/x-icon'])
                                    ->columnSpan(1),
                            ]),
                    ]),
            ]);
    }
}
