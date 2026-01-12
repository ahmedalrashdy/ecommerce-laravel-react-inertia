<?php

namespace App\Filament\Resources\Customers\Reviews\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columnSpanFull()
                    ->schema([
                        Select::make('user_id')
                            ->label(__('validation.attributes.user'))
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->searchDebounce(100)
                            ->native(false)
                            ->columnSpan(1),

                        Select::make('product_id')
                            ->label(__('validation.attributes.product'))
                            ->relationship('product', 'name')
                            ->required()
                            ->searchable()
                            ->searchDebounce(100)
                            ->native(false)
                            ->columnSpan(1),

                        TextInput::make('rating')
                            ->label(__('validation.attributes.rating'))
                            ->required()
                            ->integer()
                            ->minValue(1)
                            ->maxValue(5)
                            ->default(5)
                            ->columnSpan(1),

                        Toggle::make('is_approved')
                            ->label(__('validation.attributes.is_approved'))
                            ->default(false)
                            ->columnSpan(1),

                        Textarea::make('comment')
                            ->label(__('validation.attributes.comment'))
                            ->rows(4)
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ])
                    ->columns(4),
            ]);
    }
}
