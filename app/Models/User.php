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

// Kita implementasikan FilamentUser untuk otorisasi panel di production
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
        'role', // Termasuk 'role' agar bisa diisi
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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

    /**
     * Relasi ke toko yang dimiliki user (1 user = 1 toko)
     */
    public function store(): HasOne
    {
        return $this->hasOne(Store::class);
    }

    /**
     * Relasi yang menunjukkan produk-produk yang dimiliki user ini (melalui toko).
     */
    public function products(): HasManyThrough
    {
        return $this->hasManyThrough(
            Product::class,
            Store::class,
            'user_id', // foreign key on stores table
            'store_id', // foreign key on products table
            'id', // local key on users table
            'id' // local key on stores table
        );
    }

    /**
     * Relasi yang menunjukkan transaksi-transaksi yang dilakukan user ini (sebagai pembeli).
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'buyer_id');
    }

    /**
     * Alias untuk transactions (orders yang dibeli)
     */
    public function orders(): HasMany
    {
        return $this->transactions();
    }

    /**
     * Orders yang masuk ke toko user ini (sebagai seller)
     */
    public function storeOrders(): HasManyThrough
    {
        return $this->hasManyThrough(
            Transaction::class,
            Product::class,
            'store_id', // foreign key on products table  
            'product_id', // foreign key on transactions table
            'id', // local key on users table (melalui store)
            'id' // local key on products table
        )->whereHas('product.store', function ($query) {
            $query->where('user_id', $this->id);
        });
    }

    /**
     * Helper methods
     */
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
        // User bisa jadi seller kalau punya toko, bukan berdasarkan role
        return $this->hasStore();
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Get user's role display name
     */
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

    /**
     * Otorisasi untuk mengakses Filament Panel.
     * Hanya user dengan role 'admin' yang bisa masuk.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'admin';
    }
}
