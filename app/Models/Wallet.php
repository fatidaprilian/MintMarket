<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'user_id',
        'balance',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    // Optional: Helper tipe wallet
    public function isForStore(): bool
    {
        return (bool) $this->store_id;
    }

    public function isForUser(): bool
    {
        return (bool) $this->user_id;
    }

    // Optional: Scope wallet toko/user
    public function scopeForStore($query)
    {
        return $query->whereNotNull('store_id');
    }

    public function scopeForUser($query)
    {
        return $query->whereNotNull('user_id');
    }
}
