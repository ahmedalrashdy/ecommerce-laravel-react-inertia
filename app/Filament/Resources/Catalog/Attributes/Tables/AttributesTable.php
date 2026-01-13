<?php

namespace App\Filament\Resources\Catalog\Attributes\Tables;

use App\Enums\AttributeType;
use App\Models\Attribute;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AttributesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('values'))
            ->columns([
                TextColumn::make('name')
                    ->label(__('validation.attributes.name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('type')
                    ->label(__('validation.attributes.type'))
                    ->badge()
                    ->formatStateUsing(fn (AttributeType $state): string => $state->getLabel())
                    ->color(fn (AttributeType $state): string => match ($state) {
                        AttributeType::Text => 'gray',
                        AttributeType::Color => 'success',
                    })
                    ->sortable(),

                TextColumn::make('values_preview')
                    ->label(__('filament.attributes.values_preview'))
                    ->state(function (Attribute $record): array {
                        $values = $record->values;
                        $displayedValues = $values->take(5);
                        $totalCount = $values->count();

                        $result = $displayedValues->map(fn ($value) => $value->value)->toArray();

                        if ($totalCount > $displayedValues->count()) {
                            $remaining = $totalCount - $displayedValues->count();
                            $result[] = __('filament.attributes.and_more', ['count' => $remaining]);
                        }

                        return $result ?: [__('filament.attributes.no_values')];
                    })
                    ->badge()
                    ->color(function (Attribute $record, $state): string {
                        if ($state === __('filament.attributes.no_values')) {
                            return 'gray';
                        }

                        if (str_contains($state, __('filament.attributes.and_more'))) {
                            return 'primary';
                        }

                        if ($record->type === AttributeType::Color) {
                            $value = $record->values->firstWhere('value', $state);
                            if ($value && $value->color_code) {
                                return 'custom';
                            }
                        }

                        return 'gray';
                    })
                    ->placeholder(__('filament.attributes.no_values'))
                    ->wrap(),

                TextColumn::make('values_count')
                    ->label(__('filament.attributes.values_count'))
                    ->counts('values')
                    ->numeric()
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('validation.attributes.created_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(__('validation.attributes.type'))
                    ->options(AttributeType::class)
                    ->multiple(),
            ])
            ->recordActions([
                EditAction::make()
                    ->label(__('actions.edit')),
                DeleteAction::make()
                    ->label(__('actions.delete')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label(__('actions.delete')),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }
}
