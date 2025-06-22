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
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'address',
        'phone',
        'city',
        'province',
        'postal_code',
        'profile_picture',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // =========================================================================
    // ACCESSORS
    // =========================================================================

    public function getProfilePictureUrlAttribute(): string
    {
        if ($this->profile_picture && Storage::disk('public')->exists($this->profile_picture)) {
            return Storage::disk('public')->url($this->profile_picture);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=E8E8E8&color=757575';
    }

    public function getFullAddressAttribute(): string
    {
        $parts = [$this->address, $this->city, $this->province, $this->postal_code];
        return implode(', ', array_filter($parts));
    }

    // =========================================================================
    // RELATIONS
    // =========================================================================

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
        return $this->hasMany(Transaction::class, 'user_id');
    }

    public function orders(): HasMany
    {
        return $this->transactions();
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function wallets(): HasMany
    {
        return $this->hasMany(Wallet::class, 'user_id');
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class, 'user_id');
    }

    public function walletTopups(): HasMany
    {
        return $this->hasMany(WalletTopup::class, 'user_id');
    }

    // =========================================================================
    // HELPER METHODS
    // =========================================================================

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
        if ($this->role === 'admin') {
            return 'Administrator';
        }
        return 'Pengguna';
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'admin';
    }

    // =========================================================================
    // AUTO CREATE WALLET SAAT REGISTER
    // =========================================================================

    protected static function booted()
    {
        static::created(function ($user) {
            // Cek jika sudah ada wallet, jangan buat dua kali
            if (!$user->wallet()->exists()) {
                $user->wallet()->create(['balance' => 0]);
            }
        });
    }
}
