<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ExpertQuote extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\ExpertQuoteFactory> */
    use HasFactory;

    use InteractsWithMedia;

    protected $fillable = [
        'quote',
        'author_name',
        'author_title',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('expert_quote_images')
            ->singleFile();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getImageUrl(string $conversion = ''): string
    {
        $mediaUrl = $this->getFirstMediaUrl('expert_quote_images', $conversion);

        if (! empty($mediaUrl)) {
            return $mediaUrl;
        }

        return 'https://ui-avatars.com/api/?name='.urlencode($this->author_name).'&size=400&background=B76E79&color=fff';
    }

    public function hasImage(): bool
    {
        return $this->hasMedia('expert_quote_images');
    }
}
