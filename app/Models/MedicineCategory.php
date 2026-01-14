<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicineCategory extends Model
{
    protected $table = 'medicine_categories';
    
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the medicines for the category
     */
    public function medicines(): HasMany
    {
        return $this->hasMany(Medicine::class, 'category_id');
    }

    /**
     * Get count of medicines in this category
     */
    public function getMedicinesCountAttribute(): int
    {
        return $this->medicines()->count();
    }
}
