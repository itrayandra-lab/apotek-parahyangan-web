<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Prescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'image_path',
        'user_notes',
        'admin_notes',
        'status',
        'verified_at',
        'verified_by',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    /**
     * Get the user who uploaded this prescription
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who verified this prescription
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get the order created from this prescription
     */
    public function order(): HasOne
    {
        return $this->hasOne(PrescriptionOrder::class);
    }

    /**
     * Check if prescription is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if prescription is verified
     */
    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }

    /**
     * Check if prescription is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Get the full URL for the prescription image
     */
    public function getImageUrlAttribute(): string
    {
        return asset('storage/' . $this->image_path);
    }
}
