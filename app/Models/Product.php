<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany; // Pastikan ini diimpor
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;


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

    // GANTI (atau tambahkan jika Anda butuh relasi 'transactions' untuk hal lain)
    // public function transactions(): HasMany
    // {
    //     return $this->hasMany(Transaction::class);
    // }

    // TAMBAHKAN RELASI transactionItems() INI
    public function transactionItems(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    /**
     * ==========================================================
     * QUERY SCOPES
     * ==========================================================
     */

    public function scopeAvailable(Builder $query): void
    {
        $query->where('status', 'tersedia')
            ->where('is_active', true)
            // Pastikan toko yang memiliki produk juga aktif
            ->whereHas('store', function (Builder $q) {
                $q->where('is_active', true);
            });
    }

    public function scopeSold(Builder $query): void
    {
        $query->where('status', 'terjual');
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * ==========================================================
     * ATTRIBUTES & ACCESSORS
     * ==========================================================
     */

    public function getMainImageAttribute(): ?string
    {
        // Pastikan 'image' adalah array dan tidak kosong
        if (is_array($this->image) && count($this->image) > 0) {
            // Ambil elemen pertama yang tidak null atau string kosong dari array
            $validImages = array_filter($this->image, fn($path) => !empty($path));
            $firstImage = reset($validImages); // Mengambil elemen pertama setelah filter

            if ($firstImage && Storage::disk('public')->exists($firstImage)) {
                return Storage::url($firstImage);
            }
        }
        // Mengembalikan path ke gambar placeholder default jika tidak ada gambar atau path tidak valid
        // Pastikan Anda memiliki file ini di public/images/
        return asset('images/default-product.png');
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * ==========================================================
     * LOGIKA HARGA & DISKON TERPUSAT (FINAL)
     * ==========================================================
     */

    public function isFlashSaleActive(): bool
    {
        if ($this->flash_sale_price === null || $this->flash_sale_end_date === null) {
            return false;
        }

        // Hitung waktu mulai sesi (asumsi durasi 14 jam seperti di HomeController)
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
                    return $this->price; // Harga normal dicoret saat flash sale aktif
                }
                if ($this->hasNormalDiscount()) {
                    return $this->original_price; // Harga asli dicoret saat diskon normal
                }
                return null; // Tidak ada harga coret
            }
        );
    }

    public function getDiscountPercentageAttribute(): int
    {
        if ($this->isFlashSaleActive() && $this->price > 0) {
            // Perhitungan diskon flash sale: (Harga normal - Harga flash sale) / Harga normal
            return round((($this->price - $this->flash_sale_price) / $this->price) * 100);
        }
        if ($this->hasNormalDiscount() && $this->original_price > 0) {
            // Perhitungan diskon normal: (Harga asli - Harga jual) / Harga asli
            return round((($this->original_price - $this->price) / $this->original_price) * 100);
        }
        return 0;
    }

    public function getFormattedCurrentPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->current_price, 0, ',', '.');
    }

    public function getFormattedStrikethroughPriceAttribute(): ?string // Tambahkan return type-hint nullable string
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

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
