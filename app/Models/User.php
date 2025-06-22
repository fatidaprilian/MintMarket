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
        'province',
        'postal_code',
        'profile_picture',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // =========================================================================
    // ACCESSORS
    // =========================================================================

    /**
     * Accessor untuk mendapatkan URL gambar profil.
     * Mengembalikan URL ke gambar default jika tidak ada gambar profil.
     */
    public function getProfilePictureUrlAttribute(): string
    {
        if ($this->profile_picture && Storage::disk('public')->exists($this->profile_picture)) {
            return Storage::disk('public')->url($this->profile_picture);
        }

        // Mengembalikan gambar default dari ui-avatars.com
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=E8E8E8&color=757575';
    }

    /**
     * Accessor untuk mendapatkan alamat lengkap user.
     */
    public function getFullAddressAttribute(): string
    {
        $parts = [$this->address, $this->city, $this->province, $this->postal_code];
        return implode(', ', array_filter($parts));
    }

    // =========================================================================
    // RELATIONS
    // =========================================================================

    /**
     * Relasi ke Toko (jika user ini adalah pemilik toko)
     */
    public function store(): HasOne
    {
        return $this->hasOne(Store::class);
    }

    /**
     * Relasi ke Produk melalui Toko (produk yang dijual user ini)
     */
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

    /**
     * Relasi ke Transaksi (pesanan yang dibuat user ini)
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }

    /**
     * Alias untuk 'transactions' (jika Anda suka nama 'orders')
     */
    public function orders(): HasMany
    {
        return $this->transactions();
    }

    /**
     * Relasi ke Cart (item di keranjang user ini)
     */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Relasi ke semua wallet yang dimiliki user ini (pribadi dan/atau wallet toko).
     */
    public function wallets(): HasMany
    {
        return $this->hasMany(Wallet::class, 'user_id');
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
}