<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
        ];
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getSubtotal(): int
    {
        return $this->getPrice() * $this->quantity;
    }

    public function getPrice(): int
    {
        return $this->product->discount_price ?? $this->product->price;
    }

    public function increaseQuantity(int $amount = 1): self
    {
        $this->quantity += $amount;
        $this->save();

        return $this;
    }

    public function decreaseQuantity(int $amount = 1): self
    {
        $this->quantity = max(1, $this->quantity - $amount);
        $this->save();

        return $this;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = max(1, $quantity);
        $this->save();

        return $this;
    }
}
