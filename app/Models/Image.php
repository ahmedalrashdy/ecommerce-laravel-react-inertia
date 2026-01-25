<?php

namespace App\Models;

use App\Traits\HasFileDeletion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Image extends Model
{
    use HasFactory, HasFileDeletion;

    public function deletableFiles(): array
    {
        return [
            'path' => config('filesystems.default'),
        ];
    }
    protected $fillable = [
        'path',
        'alt_text',
        'imageable_id',
        'imageable_type',
        'display_order',
    ];
    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}
