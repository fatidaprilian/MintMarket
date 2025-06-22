<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Exception;

class EscrowService
{
    /**
     * Release dana escrow ke wallet toko setelah order selesai.
     */
    public function releaseEscrow(Transaction $transaction): bool
    {
        // Cek eligibility
        if (
            $transaction->status !== 'completed' ||
            $transaction->escrow_released_at !== null
        ) {
            throw new Exception('Transaksi belum selesai atau escrow sudah dirilis.');
        }

        $storeWallet = $transaction->store->wallet;

        if (!$storeWallet) {
            throw new Exception('Wallet toko tidak ditemukan.');
        }

        // Proses secara atomic
        DB::transaction(function () use ($transaction, $storeWallet) {
            // Tambahkan saldo ke wallet toko
            $storeWallet->balance += $transaction->total_amount;
            $storeWallet->save();

            // Buat mutasi wallet (credit)
            $storeWallet->transactions()->create([
                'amount'           => $transaction->total_amount,
                'type'             => 'credit',
                'description'      => 'Penjualan Order #' . $transaction->transaction_code,
                'running_balance'  => $storeWallet->balance,
                'reference_type'   => Transaction::class,
                'reference_id'     => $transaction->id,
            ]);

            // Tandai escrow sudah dirilis
            $transaction->escrow_released_at = now();
            $transaction->save();
        });

        return true;
    }

    /**
     * Refund dana ke wallet user jika escrow belum dirilis.
     */
    public function refundToBuyer(Transaction $transaction): bool
    {
        if ($transaction->escrow_released_at !== null) {
            throw new Exception('Escrow sudah dirilis ke toko, tidak bisa refund ke pembeli.');
        }

        $buyerWallet = $transaction->user->wallet;

        if (!$buyerWallet) {
            throw new Exception('Wallet user tidak ditemukan.');
        }

        DB::transaction(function () use ($transaction, $buyerWallet) {
            // Tambahkan saldo ke wallet user
            $buyerWallet->balance += $transaction->total_amount;
            $buyerWallet->save();

            // Buat mutasi wallet (credit)
            $buyerWallet->transactions()->create([
                'amount'           => $transaction->total_amount,
                'type'             => 'credit',
                'description'      => 'Refund Order #' . $transaction->transaction_code,
                'running_balance'  => $buyerWallet->balance,
                'reference_type'   => Transaction::class,
                'reference_id'     => $transaction->id,
            ]);
        });

        return true;
    }
}
