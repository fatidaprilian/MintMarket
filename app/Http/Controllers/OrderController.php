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
            ->with('items')
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Menampilkan detail satu pesanan.
     */
    public function show(Transaction $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $order->load('items.product', 'store');

        return view('orders.show', compact('order'));
    }

    /**
     * Proses pesanan diterima (release escrow) + LOGGING.
     * Akan mengubah status menjadi 'completed'
     */
    public function receive(Transaction $order, Request $request)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Izinkan status shipped ATAU delivered
        if (!in_array($order->status, ['shipped', 'delivered'])) {
            \Log::warning('Order status bukan shipped atau delivered', [
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'current_status' => $order->status,
            ]);
            return back()->with('error', 'Pesanan belum dapat diterima.');
        }

        try {
            \Log::info('Release escrow initiated', [
                'order_id' => $order->id,
                'current_status' => $order->status,
                'escrow_released_at' => $order->escrow_released_at,
            ]);

            $escrowService = new \App\Services\EscrowService();
            $escrowService->releaseEscrow($order);

            // UBAH STATUS JADI completed
            $order->status = 'completed';
            $order->save();

            \Log::info('Order status updated to completed', [
                'order_id' => $order->id,
                'new_status' => $order->status,
            ]);

            return back()->with('success', 'Pesanan telah diterima dan dana telah dikirim ke penjual!');
        } catch (\Exception $e) {
            \Log::error('Error on receive: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'user_id' => Auth::id(),
            ]);
            return back()->with('error', $e->getMessage());
        }
    }
}
