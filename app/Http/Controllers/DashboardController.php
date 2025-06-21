<?php

namespace App\Http\Controllers;

use App\Models\Transaction; // Import model Transaction
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use App\Models\User; // Import User model (untuk withCount)

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard pengguna.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Load user dengan counts untuk kartu statistik
        $user = Auth::user();
        $userWithCounts = User::where('id', $user->id)
            ->withCount([
                'transactions as orders_count', // Total semua transaksi user
                'transactions as completed_orders_count' => function ($query) {
                    $query->where('status', 'delivered'); // Menghitung transaksi dengan status 'delivered'
                },
                'transactions as pending_orders_count' => function ($query) {
                    // Menghitung transaksi yang masih dalam proses (pending, paid, processing, shipped)
                    $query->whereIn('status', ['pending', 'paid', 'processing', 'shipped']);
                },
                // 'wishlist as wishlist_count', // Aktifkan ini jika Anda punya relasi wishlist di model User
            ])
            ->first();

        // Fetch recent orders for the logged-in user
        $recentOrders = Transaction::where('user_id', $user->id)
            ->with('items.product', 'store') // Eager load necessary relations
            ->latest() // Order by latest transactions
            ->limit(5) // Get only the latest 5 orders
            ->get();

        return view('dashboard', [
            'recentOrders' => $recentOrders,
            'user' => $userWithCounts, // Melewatkan user dengan counts yang sudah dimuat
            // Anda bisa mengakses counts langsung di Blade seperti $user->orders_count
        ]);
    }
}
