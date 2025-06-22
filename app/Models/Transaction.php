<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

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
        'escrow_released_at', // Tambahan untuk tracking release escrow
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'escrow_released_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->transaction_code)) {
                $model->transaction_code = 'TRX-' . time() . '-' . $model->user_id;
            }
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Mark escrow as released (call this when order completed).
     */
    public function releaseEscrow()
    {
        if ($this->escrow_released_at) {
            return false;
        }
        // Di sini nanti panggil service wallet, dsb.
        $this->escrow_released_at = now();
        $this->save();
        return true;
    }

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

    public function getFormattedTotalAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}