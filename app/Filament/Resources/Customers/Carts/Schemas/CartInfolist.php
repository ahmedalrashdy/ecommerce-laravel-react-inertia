<?php

namespace App\Filament\Resources\Customers\Carts\Schemas;

use App\Filament\Schemas\Components\TimestampsSection;
use App\Models\Cart;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CartInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.carts.cart_info'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label(__('validation.attributes.user'))
                                    ->badge()
                                    ->color('info')
                                    ->placeholder('—'),

                                TextEntry::make('session_id')
                                    ->label(__('validation.attributes.session_id'))
                                    ->copyable()
                                    ->placeholder('—'),

                                TextEntry::make('created_at')
                                    ->label(__('validation.attributes.created_at'))
                                    ->dateTime('d/m/Y H:i')
                                    ->placeholder('—'),
                            ]),
                    ]),

                Section::make(__('filament.carts.statistics'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('items_count')
                                    ->label(__('filament.carts.items_count'))
                                    ->getStateUsing(fn (Cart $record): int => $record->items()->count())
                                    ->numeric()
                                    ->badge()
                                    ->color('primary'),

                                TextEntry::make('total_quantity')
                                    ->label(__('filament.carts.total_quantity'))
                                    ->getStateUsing(fn (Cart $record): int => $record->items()->sum('quantity'))
                                    ->numeric()
                                    ->badge()
                                    ->color('success'),

                                TextEntry::make('total_price')
                                    ->label(__('filament.carts.total_price'))
                                    ->getStateUsing(function (Cart $record): float {
                                        return $record->items()
                                            ->with('productVariant')
                                            ->get()
                                            ->sum(function ($item) {
                                                return $item->productVariant->price * $item->quantity;
                                            });
                                    })
                                    ->formatStateUsing(fn ($state): ?string => \App\Data\Casts\MoneyCast::format($state))
                                    ->badge()
                                    ->color('warning'),
                            ]),
                    ]),

                TimestampsSection::make(),
            ]);
    }
}
