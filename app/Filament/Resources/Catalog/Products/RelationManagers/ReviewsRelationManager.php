<?php

namespace App\Filament\Resources\Catalog\Products\RelationManagers;

use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ReviewsRelationManager extends RelationManager
{
    protected static string $relationship = 'reviews';

    public function form(Schema $schema): Schema
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
                            ->preload()
                            ->native(false)
                            ->columnSpan(1),

                        TextInput::make('rating')
                            ->label(__('validation.attributes.rating'))
                            ->required()
                            ->numeric()
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
                    ->columns(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('validation.attributes.user'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('rating')
                    ->label(__('validation.attributes.rating'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '1', '2' => 'danger',
                        '3' => 'warning',
                        '4', '5' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => $state.' ⭐')
                    ->sortable(),

                TextColumn::make('comment')
                    ->label(__('validation.attributes.comment'))
                    ->limit(50)
                    ->tooltip(fn (Model $record): ?string => $record->comment)
                    ->wrap(),

                IconColumn::make('is_approved')
                    ->label(__('validation.attributes.is_approved'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('validation.attributes.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('is_approved')
                    ->label(__('validation.attributes.is_approved'))
                    ->options([
                        true => __('filament.reviews.approved'),
                        false => __('filament.reviews.pending'),
                    ])
                    ->native(false),

                SelectFilter::make('rating')
                    ->label(__('validation.attributes.rating'))
                    ->options([
                        1 => '1 ⭐',
                        2 => '2 ⭐',
                        3 => '3 ⭐',
                        4 => '4 ⭐',
                        5 => '5 ⭐',
                    ])
                    ->native(false),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('filament.reviews.add_review')),
            ])
            ->recordActions([
                EditAction::make()
                    ->label(__('actions.edit')),
                DeleteAction::make()
                    ->label(__('actions.delete')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approve')
                        ->label(__('filament.reviews.approve'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['is_approved' => true]))
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('reject')
                        ->label(__('filament.reviews.reject'))
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['is_approved' => false]))
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make()
                        ->label(__('actions.delete')),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
