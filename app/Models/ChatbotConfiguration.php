<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $config = static::where('key', $key)
            ->where('is_active', true)
            ->first();

        return $config?->value ?? $default;
    }

    public static function setValue(string $key, mixed $value, ?string $description = null): self
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'description' => $description,
                'is_active' => true,
            ]
        );
    }

    public static function getAllActive(): array
    {
        return static::where('is_active', true)
            ->pluck('value', 'key')
            ->toArray();
    }
}
