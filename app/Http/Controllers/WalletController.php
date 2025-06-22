<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WalletTopup;

class WalletController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('wallet.transactions');
        $wallet = $user->wallet;

        // Ambil riwayat transaksi wallet terbaru
        $transactions = $wallet
            ? $wallet->transactions()->latest()->paginate(10)
            : collect();

        // Ambil riwayat top up user
        $topups = $user->walletTopups()->latest()->limit(5)->get();

        return view('wallet.index', [
            'wallet' => $wallet,
            'transactions' => $transactions,
            'topups' => $topups,
        ]);
    }

    // Tampilkan form top up
    public function topupForm()
    {
        return view('wallet.topup');
    }

    // Proses request top up
    public function topupSubmit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
            'proof'  => 'nullable|image|max:2048',
        ]);

        $user = Auth::user();

        // Simpan file bukti jika diupload
        $proofPath = null;
        if ($request->hasFile('proof')) {
            $proofPath = $request->file('proof')->store('topup_proofs', 'public');
        }

        // Catat request top up (status pending)
        WalletTopup::create([
            'user_id' => $user->id,
            'amount'  => $request->amount,
            'proof'   => $proofPath,
            'status'  => 'pending',
        ]);

        return redirect()->route('wallet.index')->with('success', 'Permintaan top up berhasil dikirim, mohon tunggu konfirmasi admin.');
    }
}
