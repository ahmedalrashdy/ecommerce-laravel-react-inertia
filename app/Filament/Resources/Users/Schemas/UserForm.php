<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.users.basic_info'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('validation.attributes.name'))
                                    ->required()
                                    ->maxLength(255),

                                Select::make('gender')
                                    ->label(__('validation.attributes.gender'))
                                    ->options([
                                        'male' => 'ذكر',
                                        'female' => 'أنثى',
                                    ])
                                    ->required(),

                                TextInput::make('email')
                                    ->label(__('validation.attributes.email'))
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),

                                TextInput::make('password')
                                    ->label(__('validation.attributes.password'))
                                    ->password()
                                    ->required(fn ($operation) => $operation === 'create')
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->default(fn ($operation) => $operation === 'create' ? 'default-password' : null)
                                    ->minLength(8)
                                    ->hidden()
                                    ->columnSpan(1),

                                TextInput::make('password_confirmation')
                                    ->label(__('validation.attributes.password_confirmation'))
                                    ->password()
                                    ->required(fn ($operation) => $operation === 'create')
                                    ->dehydrated(false)
                                    ->same('password')
                                    ->visible(fn ($get) => filled($get('password')))
                                    ->default(fn ($operation) => $operation === 'create' ? 'default-password' : null)
                                    ->hidden()
                                    ->columnSpan(1),

                                FileUpload::make('avatar')
                                    ->label(__('validation.attributes.avatar'))
                                    ->image()
                                    ->directory('avatars')
                                    ->disk('public')
                                    ->visibility('public')
                                    ->maxSize(2048)
                                    ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/jpg', 'image/webp'])
                                    ->avatar()
                                    ->columnSpanFull(),
                            ]),
                    ]),

                Section::make(__('filament.users.permissions'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Toggle::make('is_active')
                                    ->label(__('validation.attributes.is_active'))
                                    ->default(true)
                                    ->required(),
                            ]),
                    ]),
            ]);
    }
}
