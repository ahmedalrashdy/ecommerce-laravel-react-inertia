<?php

namespace App\Filament\Resources\Catalog\Attributes\RelationManagers;

use App\Enums\AttributeType;
use App\Models\AttributeValue;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ValuesRelationManager extends RelationManager
{
    protected static string $relationship = 'values';

    public function form(Schema $schema): Schema
    {
        $ownerRecord = $this->getOwnerRecord();
        $isColorType = $ownerRecord->type === AttributeType::Color;

        return $schema
            ->components([
                Section::make()
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('value')
                            ->label(__('validation.attributes.value'))
                            ->required()
                            ->maxLength(255)
                            ->unique(
                                table: AttributeValue::class,
                                column: 'value',
                                ignoreRecord: true,
                                modifyRuleUsing: function ($rule) use ($ownerRecord) {
                                    return $rule->where('attribute_id', $ownerRecord->id);
                                }
                            )
                            ->columnSpan($isColorType ? 1 : 2),
                        ColorPicker::make('color_code')
                            ->label(__('validation.attributes.color_code'))
                            ->required($isColorType)
                            ->visible($isColorType)
                            ->rule('regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/')
                            ->validationMessages([
                                'regex' => __('The color must be a valid HEX value.'),
                            ])
                            ->helperText(__(key: 'filament.attributes.color_value_helper'))
                            ->columnSpan(1),
                    ])
                    ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('value')
            ->columns([
                TextColumn::make('value')
                    ->label(__('validation.attributes.value'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                ColorColumn::make('color_code')
                    ->label(__('validation.attributes.color_code'))
                    ->visible(fn (): bool => $this->getOwnerRecord()->type === AttributeType::Color),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('filament.attributes.add_value')),
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
            ->defaultSort('value', 'asc');
    }
}
