<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'buyer_id',
        'total_amount',
        'status',
        'transaction_code',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    /**
     * Boot method untuk model.
     * Akan berjalan otomatis saat model digunakan.
     */
    protected static function boot()
    {
        parent::boot();
        // Menggunakan event 'creating' yang berjalan sebelum record disimpan
        static::creating(function ($model) {
            if (empty($model->transaction_code)) {
                $model->transaction_code = 'INV/' . date('Ymd') . '/' . strtoupper(Str::random(6));
            }
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Get store via product
     */
    public function getStoreAttribute()
    {
        return $this->product->store;
    }

    /**
     * Get seller via product->store
     */
    public function getSellerAttribute()
    {
        return $this->product->store->user;
    }

    /**
     * Scopes
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Helper methods
     */
    public function getFormattedTotalAttribute()
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isPaid()
    {
        return $this->status === 'paid';
    }
}
