<?php

namespace App\Filament\Resources\Customers\UserAddresses\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserAddressInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.user_addresses.basic_info'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label(__('validation.attributes.user'))
                                    ->columnSpan(1),

                                TextEntry::make('contact_person')
                                    ->label(__('validation.attributes.contact_person'))
                                    ->columnSpan(1),

                                TextEntry::make('contact_phone')
                                    ->label(__('validation.attributes.contact_phone'))
                                    ->copyable()
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make(__('filament.user_addresses.address_details'))
                    ->schema([
                        TextEntry::make('address_line_1')
                            ->label(__('validation.attributes.address_line_1'))
                            ->columnSpanFull(),

                        TextEntry::make('address_line_2')
                            ->label(__('validation.attributes.address_line_2'))
                            ->placeholder('—')
                            ->columnSpanFull(),

                        Grid::make(3)
                            ->schema([
                                TextEntry::make('country')
                                    ->label(__('validation.attributes.country'))
                                    ->placeholder('—')
                                    ->columnSpan(1),

                                TextEntry::make('state')
                                    ->label(__('validation.attributes.state'))
                                    ->placeholder('—')
                                    ->columnSpan(1),

                                TextEntry::make('city')
                                    ->label(__('validation.attributes.city'))
                                    ->placeholder('—')
                                    ->columnSpan(1),

                                TextEntry::make('postal_code')
                                    ->label(__('validation.attributes.postal_code'))
                                    ->placeholder('—')
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make(__('filament.user_addresses.default_settings'))
                    ->schema([
                        IconEntry::make('is_default_shipping')
                            ->label(__('validation.attributes.is_default_shipping'))
                            ->boolean(),
                    ])
                    ->columnSpanFull(),

                Section::make(__('validation.attributes.timestamps'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label(__('validation.attributes.created_at'))
                                    ->dateTime('d/m/Y H:i')
                                    ->placeholder('—')
                                    ->columnSpan(1),

                                TextEntry::make('updated_at')
                                    ->label(__('validation.attributes.updated_at'))
                                    ->dateTime('d/m/Y H:i')
                                    ->placeholder('—')
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->collapsible(),
            ]);
    }
}
