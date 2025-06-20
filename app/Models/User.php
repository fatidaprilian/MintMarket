<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Existing relations
    public function store(): HasOne
    {
        return $this->hasOne(Store::class);
    }

    public function products(): HasManyThrough
    {
        return $this->hasManyThrough(
            Product::class,
            Store::class,
            'user_id',
            'store_id',
            'id',
            'id'
        );
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'buyer_id');
    }

    public function orders(): HasMany
    {
        return $this->transactions();
    }

    public function storeOrders(): HasManyThrough
    {
        return $this->hasManyThrough(
            Transaction::class,
            Product::class,
            'store_id',
            'product_id',
            'id',
            'id'
        )->whereHas('product.store', function ($query) {
            $query->where('user_id', $this->id);
        });
    }

    // NEW: Cart relation
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    // NEW: Cart helper methods
    public function getCartItemsAttribute()
    {
        return $this->carts()->with(['product' => function ($query) {
            $query->with('store');
        }])->get();
    }

    public function getCartCountAttribute()
    {
        return $this->carts()->count();
    }

    public function getCartTotalAttribute()
    {
        return $this->carts()->with('product')->get()->sum(function ($cart) {
            return $cart->product->price * $cart->quantity;
        });
    }

    public function getCartTotalItemsAttribute()
    {
        return $this->carts()->sum('quantity');
    }

    // Existing helper methods
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function hasStore(): bool
    {
        return $this->store()->exists();
    }

    public function isSeller(): bool
    {
        return $this->hasStore();
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function getRoleDisplayAttribute(): string
    {
        if ($this->isAdmin()) {
            return 'Administrator';
        }

        if ($this->isSeller()) {
            return 'Penjual';
        }

        return 'Pembeli';
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'admin';
    }
}
