<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Get the products for the category.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
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

    public function getAvailableProductsAttribute()
    {
        return $this->products()->available()->count();
    }
}
