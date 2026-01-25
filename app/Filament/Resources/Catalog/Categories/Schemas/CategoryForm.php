<?php

namespace App\Filament\Resources\Catalog\Categories\Schemas;

use App\Enums\CategoryStatus;
use App\Models\Category;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        $isEdit = $schema->getOperation() == 'edit';

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

                        Select::make('parent_id')
                            ->label(__('validation.attributes.parent_id'))
                            ->options(function ($state, ?Category $category) {
                                if ($category) {
                                    return Category::withoutDescendants($category, true)->pluck('name', 'id');
                                }

                                return Category::pluck('name', 'id');
                            })
                            ->preload()
                            ->placeholder(__('filament.filters.all')),

                        Select::make('status')
                            ->label(__('validation.attributes.status'))
                            ->options(CategoryStatus::class)
                            ->default(CategoryStatus::Draft)
                            ->required()
                            ->native(false),

                        Textarea::make('description')
                            ->label(__('validation.attributes.description'))
                            ->rows(4)
                            ->columnSpanFull(),

                        FileUpload::make('image_path')
                            ->label(__('validation.attributes.image_path'))
                            ->image()
                            ->imageEditor()
                            ->directory('categories')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/jpg', 'image/webp'])
                            ->columnSpanFull(),
                    ])
                    ->columns(3),
            ]);
    }
}
