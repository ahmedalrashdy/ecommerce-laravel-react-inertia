<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'contact_person',
        'contact_phone',
        'country',
        'state',
        'city',
        'postal_code',
        'address_line_1',
        'address_line_2',
        'is_default_shipping',
    ];

    protected function casts(): array
    {
        return [
            'is_default_shipping' => 'boolean',
        ];
    }

    /**
     * Get the user that owns the address.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeDefaultShipping($query)
    {
        return $query->where('is_default_shipping', true);
    }
}
