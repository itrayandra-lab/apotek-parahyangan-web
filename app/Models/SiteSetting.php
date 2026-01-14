<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
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
        $setting = static::where('key', $key)
            ->where('is_active', true)
            ->first();

        return $setting?->value ?? $default;
    }

    public static function setValue(string $key, mixed $value, ?string $description = null, string $group = 'general'): self
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => (string) $value,
                'group' => $group,
                'description' => $description,
                'is_active' => true,
            ]
        );
    }

    /**
     * @return Collection<string, Collection<int, self>>
     */
    public static function getAllGrouped(): Collection
    {
        return static::query()
            ->where('is_active', true)
            ->orderBy('group')
            ->orderBy('key')
            ->get()
            ->groupBy('group');
    }

    /**
     * @return Collection<int, self>
     */
    public static function getByGroup(string $group): Collection
    {
        return static::where('group', $group)
            ->where('is_active', true)
            ->get();
    }

    public static function getAllActive(): array
    {
        return static::where('is_active', true)
            ->pluck('value', 'key')
            ->toArray();
    }
}
