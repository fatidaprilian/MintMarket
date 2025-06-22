<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;

class Store extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'logo',
        'banner',
        'province',
        'city',
        'address',
        'postal_code',
        'phone',
        'whatsapp',
        'email',
        'is_active',
        'is_verified',
        'store_type',
        'operating_hours',
        'instagram',
        'facebook',
        'tiktok',
        'terms_and_conditions',
        'rating',
        'last_active_at',
        'auto_process_orders',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'operating_hours' => 'array',
        'last_active_at' => 'datetime',
        'auto_process_orders' => 'boolean',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($store) {
            if (empty($store->slug)) {
                $store->slug = Str::slug($store->name);
            }
        });

        static::deleting(function ($store) {
            if ($store->logo && Storage::disk('public')->exists($store->logo)) {
                Storage::disk('public')->delete($store->logo);
            }
            if ($store->banner && Storage::disk('public')->exists($store->banner)) {
                Storage::disk('public')->delete($store->banner);
            }
        });
    }

    /**
     * Scope a query to only include active stores.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the user that owns the store.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all products for the store.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get all transactions for the store.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the wallet associated with the store.
     */
    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    /**
     * Get all reviews for the store through products.
     */
    public function reviews()
    {
        // Pastikan model Review ada sebelum menggunakan relasi ini
        // return $this->hasManyThrough(Review::class, Product::class);
    }

    // ==================== ACCESSORS ====================

    /**
     * Get the store logo URL.
     */
    public function getLogoUrlAttribute(): string
    {
        if ($this->logo && Storage::disk('public')->exists($this->logo)) {
            return Storage::url($this->logo);
        }
        return 'https://placehold.co/400x400/E8E8E8/757575?text=Logo';
    }

    /**
     * Get the store banner URL.
     */
    public function getBannerUrlAttribute(): string
    {
        if ($this->banner && Storage::disk('public')->exists($this->banner)) {
            return Storage::url($this->banner);
        }
        return 'https://placehold.co/1200x400/E8E8E8/757575?text=Banner';
    }

    /**
     * Get formatted total sales.
     */
    public function getFormattedTotalSalesAttribute(): string
    {
        $totalSales = $this->getTotalSales();
        return 'Rp ' . number_format($totalSales, 0, ',', '.');
    }

    /**
     * Get formatted wallet balance (saldo dompet toko).
     */
    public function getFormattedWalletBalanceAttribute(): string
    {
        $balance = $this->wallet ? $this->wallet->balance : 0;
        return 'Rp ' . number_format($balance, 0, ',', '.');
    }

    // ==================== HELPER METHODS ====================

    /**
     * Check if store profile is complete.
     */
    public function isComplete(): bool
    {
        return !empty($this->name) && !empty($this->description) &&
            !empty($this->address) && !empty($this->phone) && !empty($this->postal_code);
    }

    /**
     * Get store profile completion percentage.
     */
    public function getCompletionPercentage(): int
    {
        $fields = [
            'name',
            'description',
            'address',
            'phone',
            'postal_code',
            'logo',
            'banner',
            'whatsapp',
            'email',
            'instagram',
            'facebook',
            'tiktok',
            'operating_hours',
            'terms_and_conditions'
        ];

        $completedCount = 0;
        foreach ($fields as $field) {
            if (!empty($this->{$field})) {
                $completedCount++;
            }
        }

        return count($fields) > 0 ? round(($completedCount / count($fields)) * 100) : 0;
    }

    /**
     * Get total sales amount for the store.
     * Hanya transaksi yang sudah dicairkan ke dompet (status completed/delivered).
     */
    public function getTotalSales(): float
    {
        return $this->transactions()
            ->whereIn('status', ['completed', 'delivered'])
            ->sum('total_amount') ?? 0;
    }

    /**
     * Get total orders count.
     */
    public function getTotalOrders(): int
    {
        return $this->transactions()->count();
    }

    /**
     * Get completed orders count.
     */
    public function getCompletedOrders(): int
    {
        return $this->transactions()
            ->whereIn('status', ['completed', 'delivered'])
            ->count();
    }

    /**
     * Get pending orders count.
     */
    public function getPendingOrders(): int
    {
        return $this->transactions()
            ->whereIn('status', ['pending', 'processing', 'paid'])
            ->count();
    }

    /**
     * Get average rating from reviews.
     */
    public function getAverageRating(): float
    {
        // return $this->reviews()->avg('rating') ?? 0;
        return 0; // Placeholder
    }

    /**
     * Get total reviews count.
     */
    public function getTotalReviews(): int
    {
        // return $this->reviews()->count();
        return 0; // Placeholder
    }

    /**
     * Update store's last active timestamp.
     */
    public function updateLastActive(): void
    {
        $this->update(['last_active_at' => now()]);
    }

    /**
     * Check if store is verified.
     */
    public function isVerified(): bool
    {
        return $this->is_verified;
    }

    /**
     * Get store status badge.
     */
    public function getStatusBadge(): array
    {
        if (!$this->is_active) {
            return ['class' => 'bg-red-100 text-red-800', 'text' => 'Tidak Aktif'];
        }

        if ($this->is_verified) {
            return ['class' => 'bg-green-100 text-green-800', 'text' => 'Terverifikasi'];
        }

        return ['class' => 'bg-yellow-100 text-yellow-800', 'text' => 'Pending Verifikasi'];
    }
}
