<?php

namespace App\Http\Controllers\MyStore;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    /**
     * Display the store's wallet dashboard.
     */
    public function index()
    {
        $store = Auth::user()->store;

        // Pastikan toko ada dan memiliki wallet
        if (!$store || !$store->wallet) {
            // Jika toko ada tapi wallet tidak, buatkan wallet.
            if ($store && !$store->wallet) {
                $store->wallet()->create(['balance' => 0]);
                $store->load('wallet'); // Muat ulang relasi wallet
            } else {
                return redirect()->route('store.index')->with('error', 'Toko tidak ditemukan.');
            }
        }

        $wallet = $store->wallet;

        // Ambil riwayat transaksi wallet dengan paginasi
        $transactions = $wallet->transactions()->latest()->paginate(10);

        // Ambil daftar bank dari file config
        $banks = config('banks.list');

        return view('my-store.wallet.index', compact('store', 'wallet', 'transactions', 'banks'));
    }

    /**
     * Handle the withdrawal request.
     */
    public function storeWithdrawal(Request $request)
    {
        // Logika untuk memproses permintaan penarikan dana akan kita implementasikan nanti.
        // Untuk sekarang, kita hanya akan redirect kembali dengan pesan sukses.

        return redirect()->route('store.wallet.index')->with('success', 'Permintaan penarikan dana akan segera kami proses.');
    }
}
