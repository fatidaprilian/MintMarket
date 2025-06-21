<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Menampilkan daftar semua pesanan pengguna.
     */
    public function index()
    {
        $orders = Transaction::where('user_id', Auth::id())
            ->with('items') // Eager load a relation
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Menampilkan detail satu pesanan.
     */
    public function show(Transaction $order)
    {
        // Pastikan pengguna hanya bisa melihat pesanannya sendiri
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Load relasi item produk untuk ditampilkan di detail
        $order->load('items.product');

        return view('orders.show', compact('order'));
    }
}
