<?php

namespace App\Filament\Schemas\Components;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;

/**
 * Infolist section for created_at, updated_at and optionally deleted_at.
 * Works with models that use SoftDeletes (deleted_at shown when set) and with
 * models that do not (deleted_at stays hidden via $record?->deleted_at !== null).
 */
class TimestampsSection
{
    public static function make(): Section
    {
        return Section::make(__('validation.attributes.timestamps'))
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
                            ->visible(fn ($record): bool => $record?->deleted_at !== null)
                            ->placeholder('—')
                            ->columnSpanFull(),
                    ]),
            ])
            ->collapsible();
    }
}
