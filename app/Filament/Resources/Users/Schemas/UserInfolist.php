<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.users.basic_info'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                ImageEntry::make('avatar')
                                    ->label(__('validation.attributes.avatar'))
                                    ->circular()
                                    ->defaultImageUrl(fn (User $record) => 'https://ui-avatars.com/api/?name='.urlencode($record->name ?? '').'&background=random')
                                    ->columnSpan(1),

                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('name')
                                            ->label(__('validation.attributes.name'))
                                            ->weight('bold')
                                            ->size('lg')
                                            ->columnSpanFull(),

                                        TextEntry::make('email')
                                            ->label(__('validation.attributes.email'))
                                            ->copyable()
                                            ->copyMessage(__('filament.messages.email_copied'))
                                            ->columnSpanFull(),

                                        TextEntry::make('gender')
                                            ->label(__('validation.attributes.gender'))
                                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                                'male' => 'ذكر',
                                                'female' => 'أنثى',
                                                default => '—',
                                            }),

                                        IconEntry::make('is_admin')
                                            ->label(__('validation.attributes.is_admin'))
                                            ->boolean(),

                                        IconEntry::make('is_active')
                                            ->label(__('validation.attributes.is_active'))
                                            ->boolean(),

                                        TextEntry::make('email_verified_at')
                                            ->label(__('validation.attributes.email_verified_at'))
                                            ->dateTime('d/m/Y H:i')
                                            ->placeholder('—'),
                                    ])
                                    ->columnSpan(2),
                            ]),
                    ]),

                Section::make(__('filament.users.roles_permissions'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('roles.name')
                                    ->label(__('validation.attributes.roles'))
                                    ->badge()
                                    ->separator(',')
                                    ->color('info')
                                    ->placeholder('—')
                                    ->columnSpanFull(),

                                TextEntry::make('permissions.name')
                                    ->label(__('validation.attributes.permissions'))
                                    ->badge()
                                    ->separator(',')
                                    ->color('success')
                                    ->placeholder('—')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->visible(fn (User $record) => ! $record->is_admin),

                Section::make(__('validation.attributes.timestamps'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label(__('validation.attributes.created_at'))
                                    ->dateTime('d/m/Y H:i')
                                    ->placeholder('—'),

                                TextEntry::make('updated_at')
                                    ->label(__('validation.attributes.updated_at'))
                                    ->dateTime('d/m/Y H:i')
                                    ->placeholder('—'),

                                TextEntry::make('deleted_at')
                                    ->label(__('validation.attributes.deleted_at'))
                                    ->dateTime('d/m/Y H:i')
                                    ->visible(fn (User $record): bool => $record->deleted_at != null)
                                    ->placeholder('—')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }
}
