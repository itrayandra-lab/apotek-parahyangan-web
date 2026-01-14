<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)->latest();
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(UserAddress::class);
    }

    public function defaultAddress(): ?UserAddress
    {
        return $this->addresses()->where('is_default', true)->first()
            ?? $this->addresses()->first();
    }

    public function getCart(): Cart
    {
        return $this->cart ?? Cart::findOrCreateForUser($this);
    }

    /**
     * Update the user profile information.
     */
    public function updateProfile(array $data): void
    {
        if (isset($data['username'])) {
            $this->username = $data['username'];
        }

        if (isset($data['password']) && ! empty($data['password'])) {
            $this->password = $data['password'];
        }

        $this->save();
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is a content manager.
     */
    public function isContentManager(): bool
    {
        return $this->role === 'content_manager';
    }

    /**
     * Check if user has one of the given roles.
     *
     * @param  array<int, string>|string  $roles
     */
    public function hasRole(array|string $roles): bool
    {
        $roles = (array) $roles;

        return in_array($this->role, $roles, true);
    }
}
