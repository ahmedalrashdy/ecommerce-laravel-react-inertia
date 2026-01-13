<?php

namespace App\Filament\Resources\Catalog\Attributes\Schemas;

use App\Enums\AttributeType;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class AttributeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('validation.attributes.name'))
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->columnSpan(2),

                        Select::make('type')
                            ->label(__('validation.attributes.type'))
                            ->options(AttributeType::class)
                            ->default(AttributeType::Text)
                            ->required()
                            ->live()
                            ->native(false)
                            ->columnSpan(1),

                        Repeater::make('values')
                            ->relationship('values')
                            ->hiddenOn('edit')
                            ->columnSpanFull()
                            ->schema([
                                TextInput::make('value')
                                    ->label(__('validation.attributes.value'))
                                    ->required()
                                    ->maxLength(255)
                                    ->distinct(),

                                ColorPicker::make('color_code')
                                    ->label(__('validation.attributes.color_code'))
                                    ->required(
                                        fn (Get $get) => $get('../../type') == AttributeType::Color
                                    )
                                    ->rule('regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/')
                                    ->validationMessages([
                                        'regex' => __('The color must be a valid HEX value.'),
                                    ])
                                    ->visible(
                                        fn (Get $get) => $get('../../type') == AttributeType::Color
                                    ),
                            ])->grid(2),
                    ]),
            ]);
    }
}
