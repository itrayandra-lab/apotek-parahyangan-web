<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Tonysm\RichTextLaravel\Models\Traits\HasRichText;

class Article extends Model implements HasMedia
{
    use HasFactory;
    use HasRichText;
    use InteractsWithMedia;
    use Sluggable;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'author_id',
        'display_author_name',
        'status',
        'scheduled_at',
        'published_at',
        'views_count',
    ];

    protected $richTextAttributes = [
        'body',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'published_at' => 'datetime',
            'views_count' => 'integer',
        ];
    }

    /**
     * Get scheduled_at formatted for datetime-local input.
     * Returns in app timezone (datetime cast automatically handles this).
     */
    public function getScheduledAtForFormAttribute(): ?string
    {
        return $this->scheduled_at?->format('Y-m-d\TH:i');
    }

    public function getDisplayAuthorAttribute(): string
    {
        if (! empty($this->display_author_name)) {
            return $this->display_author_name;
        }

        return $this->author?->name ?? 'Unknown';
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
                'onUpdate' => false,
            ],
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('article_images')
            ->singleFile();
    }

    // Relationships
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ArticleCategory::class, 'article_article_category');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'article_tag');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '>', now());
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeReadyToPublish($query)
    {
        return $query->where('status', 'scheduled')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now());
    }

    public function scopePopular($query)
    {
        return $query->orderBy('views_count', 'desc');
    }

    public function scopeTrending($query)
    {
        return $query->where('published_at', '>=', now()->subDays(30))
            ->orderBy('views_count', 'desc');
    }

    // Status checkers
    public function isPublished(): bool
    {
        return $this->status === 'published'
            && $this->published_at !== null
            && $this->published_at <= now();
    }

    public function isScheduled(): bool
    {
        return $this->status === 'scheduled'
            && $this->scheduled_at !== null
            && $this->scheduled_at > now();
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    // Publishing methods
    public function publish(): void
    {
        $this->update([
            'status' => 'published',
            'published_at' => now(),
            'scheduled_at' => null,
        ]);
    }

    public function schedule(\DateTime $dateTime): void
    {
        $this->update([
            'status' => 'scheduled',
            'scheduled_at' => $dateTime,
            'published_at' => null,
        ]);
    }

    public function unschedule(): void
    {
        $this->update([
            'status' => 'draft',
            'scheduled_at' => null,
            'published_at' => null,
        ]);
    }

    // View counting
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    // Related articles
    public function getRelatedArticles(int $limit = 4)
    {
        // Get article IDs sharing the most tags
        $tagIds = $this->tags()->pluck('tags.id');

        if ($tagIds->isEmpty()) {
            // Fallback: articles from same categories
            return static::published()
                ->whereHas('categories', function ($query) {
                    $query->whereIn('article_categories.id', $this->categories()->pluck('article_categories.id'));
                })
                ->where('id', '!=', $this->id)
                ->popular()
                ->limit($limit)
                ->get();
        }

        return static::published()
            ->whereHas('tags', function ($query) use ($tagIds) {
                $query->whereIn('tags.id', $tagIds);
            })
            ->where('id', '!=', $this->id)
            ->withCount(['tags as shared_tags_count' => function ($query) use ($tagIds) {
                $query->whereIn('tags.id', $tagIds);
            }])
            ->orderBy('shared_tags_count', 'desc')
            ->orderBy('views_count', 'desc')
            ->limit($limit)
            ->get();
    }

    // Media helpers
    public function getImageUrl(): ?string
    {
        $url = $this->getFirstMediaUrl('article_images');
        
        if ($url) {
            $path = parse_url($url, PHP_URL_PATH);
            return $path ? '/' . ltrim($path, '/') : null;
        }
        
        return null;
    }

    public function hasImage(): bool
    {
        return $this->hasMedia('article_images');
    }

    public function getImage()
    {
        return $this->getFirstMedia('article_images');
    }
}
