<?php

namespace App\Models;

use App\Enums\ReturnStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ReturnHistory extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'status' => ReturnStatus::class,
            'is_visible_to_user' => 'boolean',
        ];
    }

    public function returnOrder(): BelongsTo
    {
        return $this->belongsTo(ReturnOrder::class, 'return_id');
    }

    public function actor(): MorphTo
    {
        return $this->morphTo();
    }
}
