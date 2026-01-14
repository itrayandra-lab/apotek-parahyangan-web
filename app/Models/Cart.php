<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'guest_session_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function getItemsCount(): int
    {
        return (int) $this->items()->sum('quantity');
    }

    public function getSubtotal(): int
    {
        return (int) $this->items->sum(fn (CartItem $item): int => $item->getSubtotal());
    }

    public function getTax(): int
    {
        return 0;
    }

    public function getShippingCost(): int
    {
        return (int) config('cart.shipping_cost', 0);
    }

    public function getTotal(): int
    {
        return $this->getSubtotal() + $this->getShippingCost();
    }

    public function addProduct(Product $product, int $quantity = 1): CartItem
    {
        $cartItem = $this->items()->where('product_id', $product->id)->first();

        if ($cartItem) {
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            $cartItem = $this->items()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
            ]);
        }

        return $cartItem;
    }

    public function removeProduct(Product $product): bool
    {
        return (bool) $this->items()->where('product_id', $product->id)->delete();
    }

    public function updateQuantity(Product $product, int $quantity): ?CartItem
    {
        $cartItem = $this->items()->where('product_id', $product->id)->first();

        if (! $cartItem) {
            return null;
        }

        $cartItem->quantity = $quantity;
        $cartItem->save();

        return $cartItem;
    }

    public function clear(): void
    {
        $this->items()->delete();
    }

    public function mergeWith(Cart $guestCart): void
    {
        foreach ($guestCart->items as $guestItem) {
            $existingItem = $this->items()->where('product_id', $guestItem->product_id)->first();

            if ($existingItem) {
                $existingItem->quantity += $guestItem->quantity;
                $existingItem->save();

                continue;
            }

            $this->items()->create([
                'product_id' => $guestItem->product_id,
                'quantity' => $guestItem->quantity,
            ]);
        }

        $guestCart->delete();
    }

    public static function findOrCreateForUser(User $user): self
    {
        return self::firstOrCreate(['user_id' => $user->id]);
    }

    public static function findOrCreateForGuest(string $sessionId): self
    {
        return self::firstOrCreate(['guest_session_id' => $sessionId]);
    }

    public static function currentCart(): self
    {
        if (auth()->check()) {
            return self::findOrCreateForUser(auth()->user());
        }

        return self::findOrCreateForGuest(session()->getId());
    }
}
