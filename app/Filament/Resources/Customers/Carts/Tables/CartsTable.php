<?php

namespace App\Filament\Resources\Customers\Carts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class CartsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query): Builder {
                $query->with(['user', 'items.productVariant.product'])
                    ->withSum('items', 'quantity')
                    ->addSelect([
                        DB::raw('(SELECT COALESCE(SUM(ci.quantity * pv.price), 0) FROM cart_items ci INNER JOIN product_variants pv ON ci.product_variant_id = pv.id WHERE ci.cart_id = carts.id) as cart_total_price'),
                    ]);

                return $query;
            })
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('validation.attributes.user'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('session_id')
                    ->label(__('validation.attributes.session_id'))
                    ->searchable()
                    ->copyable()
                    ->toggleable(),

                TextColumn::make('items_count')
                    ->label(__('filament.carts.items_count'))
                    ->counts('items')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('items_sum_quantity')
                    ->label(__('filament.carts.total_quantity'))
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->default(0),

                TextColumn::make('cart_total_price')
                    ->label(__('filament.carts.total_price'))
                    ->formatStateUsing(fn ($state): ?string => \App\Data\Casts\MoneyCast::format($state))
                    ->sortable()
                    ->badge()
                    ->color('warning'),

                TextColumn::make('created_at')
                    ->label(__('validation.attributes.created_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('validation.attributes.updated_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('user_or_guest')
                    ->label(__('filament.carts.user_or_guest'))
                    ->form([
                        Select::make('user_or_guest')
                            ->options([
                                'user' => __('filament.filters.user_only'),
                                'guest' => __('filament.filters.guest_only'),
                            ])
                            ->placeholder(__('filament.filters.all')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $v = $data['user_or_guest'] ?? null;
                        if ($v === 'user') {
                            return $query->whereNotNull('user_id');
                        }
                        if ($v === 'guest') {
                            return $query->whereNull('user_id');
                        }

                        return $query;
                    }),

                SelectFilter::make('user_id')
                    ->label(__('validation.attributes.user'))
                    ->relationship('user', 'name')
                    ->preload()
                    ->multiple(),

                Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from')
                            ->label(__('filament.filters.created_from')),
                        \Filament\Forms\Components\DatePicker::make('created_until')
                            ->label(__('filament.filters.created_until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label(__('actions.view')),
                DeleteAction::make()
                    ->label(__('actions.delete')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label(__('actions.delete')),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
