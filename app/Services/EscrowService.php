<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Exception;

class EscrowService
{
    /**
     * Release dana escrow ke wallet toko setelah order delivered.
     */
    public function releaseEscrow(Transaction $transaction): bool
    {
        // Cek eligibility: status delivered dan belum pernah release
        if (
            $transaction->status !== 'delivered' ||
            $transaction->escrow_released_at !== null // ini harus kamu tambahkan ke model & tabel transactions
        ) {
            throw new Exception('Transaksi belum delivered atau escrow sudah dirilis.');
        }

        $storeWallet = $transaction->store->wallet;

        if (!$storeWallet) {
            throw new Exception('Wallet toko tidak ditemukan.');
        }

        DB::transaction(function () use ($transaction, $storeWallet) {
            // Tambahkan saldo ke wallet toko
            $storeWallet->balance += $transaction->total_amount;
            $storeWallet->save();

            // Mutasi wallet (credit)
            $storeWallet->transactions()->create([
                'amount'          => $transaction->total_amount,
                'type'            => 'credit',
                'description'     => 'Penjualan Order #' . $transaction->transaction_code,
                'running_balance' => $storeWallet->balance,
                'reference_type'  => Transaction::class,
                'reference_id'    => $transaction->id,
            ]);

            // Tandai escrow sudah dirilis
            $transaction->escrow_released_at = now();
            $transaction->save();
        });

        return true;
    }

    // Refund ke user jika diperlukan, mirip dengan logic di atas.
}
