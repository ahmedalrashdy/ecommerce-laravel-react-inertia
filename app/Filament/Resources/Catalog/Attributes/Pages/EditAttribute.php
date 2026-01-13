<?php

namespace App\Filament\Resources\Catalog\Attributes\Pages;

use App\Enums\AttributeType;
use App\Filament\Resources\Catalog\Attributes\AttributeResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAttribute extends EditRecord
{
    protected static string $resource = AttributeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        if ($this->shouldRequireConfirmation()) {
            return Action::make('save')
                ->requiresConfirmation()
                ->modalHeading(__('filament.attributes.save_changes_heading'))
                ->modalDescription($this->getConfirmationDescription())
                ->modalSubmitActionLabel(__('filament.attributes.save_changes_confirm'))
                ->color('primary')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->action(fn ($record) => $record->update($this->data))
                ->keyBindings(['mod+s']);
        }

        return parent::getSaveFormAction();
    }

    protected function shouldRequireConfirmation(): bool
    {
        $originalType = $this->record->getOriginal('type');

        $newType = $this->data['type'];

        // dd($originalType == AttributeType::Color && $newType == AttributeType::Text->value);
        return $originalType == AttributeType::Color && $newType == AttributeType::Text->value;
    }

    protected function getConfirmationDescription(): string
    {
        $valuesCount = $this->record->values()->whereNotNull('color_code')->count();

        return __('filament.attributes.type_change_confirmation_body', ['count' => $valuesCount]);
    }
}
