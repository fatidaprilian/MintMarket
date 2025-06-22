<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WalletTransaction extends Model
{
    use HasFactory;

    // (Optional) Enum type, jika pakai PHP 8.1+
    // public const TYPE_CREDIT = 'credit';
    // public const TYPE_DEBIT = 'debit';

    protected $fillable = [
        'wallet_id',
        'reference_type',
        'reference_id',
        'amount',
        'type',
        'description',
        'running_balance',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'running_balance' => 'decimal:2',
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    protected static function booted()
    {
        static::creating(function (self $transaction) {
            // Ambil transaksi terakhir untuk wallet terkait
            $last = self::where('wallet_id', $transaction->wallet_id)
                ->orderByDesc('id')
                ->first();

            $lastBalance = $last?->running_balance ?? 0;

            // Penyesuaian saldo (credit = tambah, debit = kurang)
            $isCredit = in_array($transaction->type, [
                'credit'
            ]);

            $newBalance = $isCredit
                ? $lastBalance + $transaction->amount
                : $lastBalance - $transaction->amount;

            $transaction->running_balance = $newBalance;

            // Sync ke Wallet (pastikan selalu sama)
            $wallet = $transaction->wallet;
            if ($wallet) {
                $wallet->balance = $newBalance;
                $wallet->save();
            }
        });
    }
}
