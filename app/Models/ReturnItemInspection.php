<?php

namespace App\Models;

use App\Enums\ItemCondition;
use App\Enums\ReturnResolution;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnItemInspection extends Model
{
    /** @use HasFactory<\Database\Factories\ReturnItemInspectionFactory> */
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'condition' => ItemCondition::class,
            'resolution' => ReturnResolution::class,
            'refund_amount' => 'string',
        ];
    }

    public function returnItem(): BelongsTo
    {
        return $this->belongsTo(ReturnItem::class);
    }
}
