<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard pengguna.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Load user dengan counts untuk kartu statistik dan relasi wallet
        $user = Auth::user();
        $userWithCounts = User::where('id', $user->id)
            ->withCount([
                'transactions as orders_count',
                'transactions as completed_orders_count' => function ($query) {
                    $query->where('status', 'delivered');
                },
                'transactions as pending_orders_count' => function ($query) {
                    $query->whereIn('status', ['pending', 'paid', 'processing', 'shipped']);
                },
                // 'wishlist as wishlist_count', // Aktifkan jika ada relasi wishlist di model User
            ])
            ->with(['wallet']) // Tambahkan eager load wallet di sini
            ->first();

        // Fetch recent orders for the logged-in user
        $recentOrders = Transaction::where('user_id', $user->id)
            ->with('items.product', 'store')
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', [
            'recentOrders' => $recentOrders,
            'user' => $userWithCounts,
        ]);
    }
}