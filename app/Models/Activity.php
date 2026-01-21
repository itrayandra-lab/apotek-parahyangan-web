<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'description',
        'reference_id',
        'reference_type',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
    
    /**
     * Get formatted time ago
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }
    
    /**
     * Get activity icon based on type
     */
    public function getIconAttribute(): string
    {
        return match ($this->type) {
            'order' => 'shopping-bag',
            'prescription' => 'document-text',
            'payment' => 'credit-card',
            default => 'bell',
        };
    }
    
    /**
     * Get activity color based on type and status
     */
    public function getColorAttribute(): string
    {
        if ($this->type === 'order') {
            $status = $this->metadata['status'] ?? '';
            return match ($status) {
                'pending_payment' => 'yellow',
                'confirmed', 'processing' => 'blue',
                'ready_for_pickup', 'completed' => 'green',
                'cancelled', 'expired' => 'red',
                default => 'gray',
            };
        }
        
        if ($this->type === 'prescription') {
            $status = $this->metadata['verification_status'] ?? '';
            return match ($status) {
                'pending' => 'yellow',
                'verified' => 'green',
                'rejected' => 'red',
                default => 'gray',
            };
        }
        
        return 'gray';
    }
}