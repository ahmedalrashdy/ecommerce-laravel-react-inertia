<?php

namespace App\Observers;

use App\Enums\AttributeType;
use App\Models\Attribute;

class AttributeObserver
{
    /**
     * Handle the Attribute "created" event.
     */
    public function created(Attribute $attribute): void
    {
        //
    }

    /**
     * Handle the Attribute "updated" event.
     */
    public function updated(Attribute $attribute): void
    {
        if ($attribute->wasChanged('type')) {
            $originalType = $attribute->getOriginal('type');
            $newType = $attribute->type;

            if ($originalType === AttributeType::Color && $newType === AttributeType::Text) {
                $attribute->values()->update(['color_code' => null]);
            }
        }
    }

    /**
     * Handle the Attribute "deleted" event.
     */
    public function deleted(Attribute $attribute): void
    {
        //
    }

    /**
     * Handle the Attribute "restored" event.
     */
    public function restored(Attribute $attribute): void
    {
        //
    }

    /**
     * Handle the Attribute "force deleted" event.
     */
    public function forceDeleted(Attribute $attribute): void
    {
        //
    }
}
