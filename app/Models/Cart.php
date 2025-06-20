<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity'
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getSubtotalAttribute()
    {
        return $this->product->price * $this->quantity;
    }

    public static function getCartItemCount($userId)
    {
        return self::where('user_id', $userId)->count();
    }

    public static function getCartTotal($userId)
    {
        return self::where('user_id', $userId)
            ->with('product')
            ->get()
            ->sum(function ($cart) {
                return $cart->product->price * $cart->quantity;
            });
    }

    public static function getTotalItems($userId)
    {
        return self::where('user_id', $userId)->sum('quantity');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }
}
