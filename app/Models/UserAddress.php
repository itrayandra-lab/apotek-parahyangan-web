<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAddress extends Model
{
    use HasFactory;

    public const MAX_ADDRESSES_PER_USER = 5;

    protected $fillable = [
        'user_id',
        'label',
        'recipient_name',
        'phone',
        'address',
        'province_code',
        'province_name',
        'city_code',
        'city_name',
        'district_code',
        'district_name',
        'village_code',
        'village_name',
        'postal_code',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function setAsDefault(): void
    {
        // Remove default from other addresses
        self::query()
            ->where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        $this->update(['is_default' => true]);
    }

    public function getFullAddressAttribute(): string
    {
        return implode(', ', array_filter([
            $this->address,
            $this->village_name,
            $this->district_name,
            $this->city_name,
            $this->province_name,
            $this->postal_code,
        ]));
    }

    public function getShortAddressAttribute(): string
    {
        return implode(', ', array_filter([
            $this->address,
            $this->city_name,
            $this->province_name,
        ]));
    }
}
