<?php

namespace App\Filament\Resources\Customers\UserAddresses\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserAddressForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.user_addresses.basic_info'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('user_id')
                                    ->label(__('validation.attributes.user'))
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpan(1),

                                TextInput::make('contact_person')
                                    ->label(__('validation.attributes.contact_person'))
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(1),

                                TextInput::make('contact_phone')
                                    ->label(__('validation.attributes.contact_phone'))
                                    ->tel()
                                    ->required()
                                    ->maxLength(20)
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make(__('filament.user_addresses.address_details'))
                    ->schema([
                        TextInput::make('address_line_1')
                            ->label(__('validation.attributes.address_line_1'))
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TextInput::make('address_line_2')
                            ->label(__('validation.attributes.address_line_2'))
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('country')
                                    ->label(__('validation.attributes.country'))
                                    ->maxLength(100)
                                    ->columnSpan(1),

                                TextInput::make('state')
                                    ->label(__('validation.attributes.state'))
                                    ->maxLength(100)
                                    ->columnSpan(1),

                                TextInput::make('city')
                                    ->label(__('validation.attributes.city'))
                                    ->maxLength(100)
                                    ->columnSpan(1),

                                TextInput::make('postal_code')
                                    ->label(__('validation.attributes.postal_code'))
                                    ->maxLength(20)
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make(__('filament.user_addresses.default_settings'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Toggle::make('is_default_shipping')
                                    ->label(__('validation.attributes.is_default_shipping'))
                                    ->default(false)
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
