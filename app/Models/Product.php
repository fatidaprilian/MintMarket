<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'store_id',
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'original_price', // TAMBAHAN BARU
        'condition',
        'stock', // Tambah ini juga kalau belum ada
        'image',
        'status',
        'is_active', // Tambah ini juga kalau belum ada
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'image' => 'array',
        'price' => 'decimal:2',
        'original_price' => 'decimal:2', // TAMBAHAN BARU
        'is_active' => 'boolean',
    ];

    /**
     * Relasi produk ke toko yang menjualnya.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Relasi produk ke kategorinya.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi ke transactions
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get user (owner) melalui store
     */
    public function getUserAttribute()
    {
        return $this->store->user;
    }

    /**
     * Scopes
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'tersedia')
            ->where('is_active', true);
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
     * Route key name untuk model binding
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Helper methods & Accessors
     */
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    // ACCESSOR BARU UNTUK FLASH SALE
    public function getFormattedOriginalPriceAttribute()
    {
        return 'Rp ' . number_format($this->original_price, 0, ',', '.');
    }

    // ACCESSOR BARU UNTUK DISCOUNT PERCENTAGE
    public function getDiscountPercentageAttribute()
    {
        if ($this->original_price && $this->original_price > $this->price) {
            return round((($this->original_price - $this->price) / $this->original_price) * 100);
        }
        return 0;
    }

    // ACCESSOR UNTUK CHECK APAKAH PRODUK SEDANG DISKON
    public function getIsOnSaleAttribute()
    {
        return $this->original_price && $this->original_price > $this->price;
    }

    public function getMainImageAttribute()
    {
        if (is_array($this->image) && count($this->image) > 0) {
            return $this->image[0];
        }
        return $this->image;
    }

    public function isAvailable()
    {
        return $this->status === 'tersedia' && $this->is_active;
    }

    // HELPER METHOD BARU UNTUK FLASH SALE
    public function isFlashSale()
    {
        return $this->is_on_sale;
    }
}
