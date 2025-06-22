<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Str;

class Withdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'withdrawal_code',
        'amount',
        'status',
        'bank_name',
        'account_holder_name',
        'account_number',
        'rejection_reason',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Membuat withdrawal_code secara otomatis saat record baru dibuat
        static::creating(function ($model) {
            if (empty($model->withdrawal_code)) {
                $model->withdrawal_code = 'WDL-' . strtoupper(Str::random(10));
            }
        });
    }

    /**
     * Get the wallet that this withdrawal belongs to.
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Get the wallet transaction record associated with the withdrawal.
     */
    public function walletTransaction(): MorphOne
    {
        return $this->morphOne(WalletTransaction::class, 'reference');
    }
}
