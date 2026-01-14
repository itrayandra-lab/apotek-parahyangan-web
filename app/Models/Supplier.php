<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $table = 'suppliers';
    
    protected $fillable = [
        'code',
        'name',
        'address',
        'phone',
        'email',
        'bank_account',
        'notes',
    ];

    /**
     * Get the medicines supplied by this supplier
     */
    public function medicines(): HasMany
    {
        return $this->hasMany(Medicine::class, 'main_supplier_id');
    }
}
