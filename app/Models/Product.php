<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'original_price',
        'flash_sale_price',
        'flash_sale_end_date',
        'condition',
        'stock',
        'image',
        'status',
        'is_active',
    ];

    protected $casts = [
        'image' => 'array',
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'flash_sale_price' => 'decimal:2',
        'flash_sale_end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * ==========================================================
     * RELATIONSHIPS
     * ==========================================================
     */

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * ==========================================================
     * QUERY SCOPES
     * ==========================================================
     */

    public function scopeAvailable($query)
    {
        return $query->where('status', 'tersedia')->where('is_active', true);
    }

    public function scopeSold($query)
    {
        return $query->where('status', 'terjual');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * ==========================================================
     * ATTRIBUTES & ACCESSORS
     * ==========================================================
     */

    public function getMainImageAttribute()
    {
        if (is_array($this->image) && count($this->image) > 0) {
            return $this->image[0];
        }
        return $this->image;
    }

    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * ==========================================================
     * LOGIKA HARGA & DISKON TERPUSAT (FINAL)
     * ==========================================================
     */

    /**
     * PERBAIKAN: Cek apakah flash sale sedang berlangsung SEKARANG.
     * Logika ini sekarang memeriksa waktu mulai dan waktu berakhir sesi.
     */
    public function isFlashSaleActive(): bool
    {
        if ($this->flash_sale_price === null || $this->flash_sale_end_date === null) {
            return false;
        }

        // Hitung waktu mulai sesi (dengan asumsi durasi 14 jam)
        $sessionStartTime = $this->flash_sale_end_date->copy()->subHours(14);

        // Return true HANYA jika waktu saat ini berada di antara waktu mulai dan berakhir
        return now()->between($sessionStartTime, $this->flash_sale_end_date);
    }

    public function hasNormalDiscount(): bool
    {
        return !$this->isFlashSaleActive() &&
            $this->original_price !== null &&
            $this->original_price > $this->price;
    }

    protected function currentPrice(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->isFlashSaleActive() ? $this->flash_sale_price : $this->price
        );
    }

    protected function strikethroughPrice(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->isFlashSaleActive()) {
                    return $this->price;
                }
                if ($this->hasNormalDiscount()) {
                    return $this->original_price;
                }
                return null;
            }
        );
    }

    public function getDiscountPercentageAttribute(): int
    {
        if ($this->isFlashSaleActive()) {
            return round((($this->price - $this->flash_sale_price) / $this->price) * 100);
        }
        if ($this->hasNormalDiscount()) {
            return round((($this->original_price - $this->price) / $this->original_price) * 100);
        }
        return 0;
    }

    public function getFormattedCurrentPriceAttribute()
    {
        return 'Rp ' . number_format($this->current_price, 0, ',', '.');
    }

    public function getFormattedStrikethroughPriceAttribute()
    {
        if ($this->strikethrough_price) {
            return 'Rp ' . number_format($this->strikethrough_price, 0, ',', '.');
        }
        return null;
    }

    /**
     * ==========================================================
     * ROUTE MODEL BINDING
     * ==========================================================
     */

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
