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

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'address',
        'phone',
        'city',
        'postal_code',
        'profile_picture', // <-- Tambahkan ini
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

    // Relasi ke Toko (jika user ini adalah pemilik toko)
    public function store(): HasOne
    {
        return $this->hasOne(Store::class);
    }

    // Relasi ke Produk melalui Toko (produk yang dijual user ini)
    public function products(): HasManyThrough
    {
        return $this->hasManyThrough(
            Product::class,
            Store::class,
            'user_id', // Foreign key on stores table
            'store_id', // Foreign key on products table
            'id', // Local key on users table
            'id' // Local key on stores table
        );
    }

    // Relasi ke Transaksi (pesanan yang dibuat user ini)
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }

    // Alias untuk 'transactions' (jika Anda suka nama 'orders')
    public function orders(): HasMany
    {
        return $this->transactions();
    }

    // Relasi ke Cart (item di keranjang user ini)
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    // Helper methods (Opsional, Anda bisa menghitung ini di controller dengan withCount)
    // Jika Anda ingin memiliki accessors ini:
    // public function getOrdersCountAttribute()
    // {
    //     return $this->transactions()->count();
    // }

    // public function getCompletedOrdersCountAttribute()
    // {
    //     return $this->transactions()->where('status', 'delivered')->count();
    // }

    // public function getPendingOrdersCountAttribute()
    // {
    //     return $this->transactions()->whereIn('status', ['pending', 'paid', 'processing', 'shipped'])->count();
    // }

    // public function getWishlistCountAttribute()
    // {
    //     // Implementasi untuk menghitung wishlist jika ada
    //     return 0;
    // }

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
