<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
// use Illuminate\Database\Eloquent\SoftDeletes; // Hapus ini

class Product extends Model
{
    use HasFactory; // Hapus SoftDeletes

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
        'condition',
        'image',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'image' => 'array',
        'price' => 'decimal:2',
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
        return $query->where('status', 'tersedia');
    }

    public function scopeSold($query)
    {
        return $query->where('status', 'terjual');
    }

    /**
     * Route key name untuk model binding
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Helper methods
     */
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
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
        return $this->status === 'tersedia';
    }
}
