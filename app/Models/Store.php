<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
// use Illuminate\Database\Eloquent\SoftDeletes; // Hapus ini

class Store extends Model
{
    use HasFactory; // Hapus SoftDeletes

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'is_active',
        'province',
        'city',
        'address',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Orders yang masuk ke toko ini
     */
    public function orders(): HasManyThrough
    {
        return $this->hasManyThrough(
            Transaction::class,
            Product::class,
            'store_id', // foreign key on products table
            'product_id', // foreign key on transactions table
            'id', // local key on stores table
            'id' // local key on products table
        );
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Route key name
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Helper methods
     */
    public function getTotalProductsAttribute()
    {
        return $this->products()->count();
    }

    public function getTotalOrdersAttribute()
    {
        return $this->orders()->count();
    }
}
