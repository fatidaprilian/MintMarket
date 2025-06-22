<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'store_id',
        'transaction_code',
        'total_amount',
        'shipping_cost',
        'shipping_address',
        'status',
        'payment_method',
        'shipping_method',
        'tracking_number',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        // Membuat transaction_code secara otomatis sebelum menyimpan record baru
        static::creating(function ($model) {
            if (empty($model->transaction_code)) {
                $model->transaction_code = 'TRX-' . time() . '-' . $model->user_id;
            }
        });
    }

    /**
     * Mendapatkan semua item detail dari transaksi ini.
     */
    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    /**
     * Mendapatkan pengguna (pembeli) yang memiliki transaksi ini.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Mendapatkan toko yang terkait dengan transaksi ini.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    // =========================================================================
    // ACCESSORS
    // =========================================================================

    /**
     * Accessor untuk mendapatkan badge status (class & text).
     *
     * @return array
     */
    public function getStatusBadgeAttribute(): array
    {
        $statusMap = [
            'pending'    => ['class' => 'bg-yellow-100 text-yellow-800', 'text' => 'Menunggu Pembayaran'],
            'paid'       => ['class' => 'bg-cyan-100 text-cyan-800',   'text' => 'Telah Dibayar'],
            'processing' => ['class' => 'bg-blue-100 text-blue-800',   'text' => 'Diproses'],
            'shipped'    => ['class' => 'bg-purple-100 text-purple-800', 'text' => 'Dikirim'],
            'completed'  => ['class' => 'bg-green-100 text-green-800',  'text' => 'Selesai'],
            'cancelled'  => ['class' => 'bg-red-100 text-red-800',    'text' => 'Dibatalkan'],
        ];

        return $statusMap[$this->status] ?? ['class' => 'bg-gray-100 text-gray-800', 'text' => ucfirst($this->status)];
    }

    /**
     * Accessor untuk format total harga.
     *
     * @return string
     */
    public function getFormattedTotalAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    /**
     * Scope a query to only include transactions with a given status.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
