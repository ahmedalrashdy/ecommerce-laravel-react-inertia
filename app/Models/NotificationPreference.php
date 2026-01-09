<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    /** @use HasFactory<\Database\Factories\NotificationPreferenceFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'marketing_email',
        'marketing_sms',
        'marketing_whatsapp',
        'marketing_call',
    ];

    protected function casts(): array
    {
        return [
            'marketing_email' => 'boolean',
            'marketing_sms' => 'boolean',
            'marketing_whatsapp' => 'boolean',
            'marketing_call' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
