<?php

namespace App\Filament\Resources\Catalog\Brands\Schemas;

use App\Enums\BrandStatus;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BrandForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('validation.attributes.name'))
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->columnSpan(2),

                        Select::make('status')
                            ->label(__('validation.attributes.status'))
                            ->options(BrandStatus::class)
                            ->default(BrandStatus::Draft)
                            ->required()
                            ->native(false),

                        Toggle::make('featured')
                            ->label(__('validation.attributes.featured'))
                            ->default(false),

                        Textarea::make('description')
                            ->label(__('validation.attributes.description'))
                            ->rows(4)
                            ->columnSpanFull(),

                        FileUpload::make('image_path')
                            ->label(__('validation.attributes.image_path'))
                            ->image()
                            ->required()
                            ->imageEditor()
                            ->directory('brands')
                            ->disk('public')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/jpg', 'image/webp'])
                            ->columnSpanFull(),
                    ])
                    ->columns(3),
            ]);
    }
}
