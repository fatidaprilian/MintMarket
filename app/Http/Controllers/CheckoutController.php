<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    /**
     * Menampilkan halaman checkout.
     */
    public function index()
    {
        $user = Auth::user();
        $cartItems = Cart::where('user_id', $user->id)->with('product.store')->get();

        // Validasi keranjang
        $hasChanges = false;
        foreach ($cartItems as $key => $cart) {
            $product = $cart->product;

            if (!$product || $product->status !== 'tersedia') {
                $cart->delete();
                $cartItems->forget($key);
                $hasChanges = true;
                continue;
            }

            if ($cart->quantity > $product->stock) {
                if ($product->stock > 0) {
                    $cart->update(['quantity' => $product->stock]);
                    $cart->quantity = $product->stock;
                } else {
                    $cart->delete();
                    $cartItems->forget($key);
                }
                $hasChanges = true;
            }
        }

        if ($hasChanges) {
            return redirect()->route('cart.index')->with('info', 'Keranjang Anda telah disesuaikan dengan stok dan ketersediaan produk terbaru.');
        }

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang Anda kosong. Silakan tambahkan produk terlebih dahulu.');
        }

        // Grup items berdasarkan toko
        $groupedItems = $cartItems->groupBy('product.store_id');

        // Hitung total untuk setiap toko
        $storeSubtotals = [];
        $totalSubtotal = 0;

        foreach ($groupedItems as $storeId => $storeItems) {
            $storeSubtotal = $storeItems->sum(function ($item) {
                return $item->product->current_price * $item->quantity;
            });
            $storeSubtotals[$storeId] = $storeSubtotal;
            $totalSubtotal += $storeSubtotal;
        }

        // Ongkos kirim per toko
        $shippingCostPerStore = 15000;
        $totalShippingCost = count($groupedItems) * $shippingCostPerStore;
        $grandTotal = $totalSubtotal + $totalShippingCost;

        return view('checkout.index', [
            'cartItems' => $cartItems,
            'groupedItems' => $groupedItems,
            'storeSubtotals' => $storeSubtotals,
            'subtotal' => $totalSubtotal,
            'shippingCost' => $totalShippingCost,
            'total' => $grandTotal,
            'user' => $user, // Pass user untuk akses alamat
        ]);
    }

    /**
     * Memproses pesanan dari form checkout.
     */
    public function process(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string|max:1000',
            'shipping_method' => 'required|string',
            'payment_method' => 'required|string',
            'save_address' => 'boolean', // Checkbox untuk menyimpan alamat
        ]);

        $user = Auth::user();

        try {
            DB::beginTransaction();

            // Simpan alamat ke profil user jika diminta
            if ($request->has('save_address') && $request->save_address) {
                $user->update(['address' => $request->shipping_address]);
            }

            $cartItems = Cart::where('user_id', $user->id)->with('product.store')->get();

            if ($cartItems->isEmpty()) {
                return redirect()->route('home')->with('error', 'Checkout gagal karena keranjang Anda kosong.');
            }

            // Grup items berdasarkan toko
            $groupedItems = $cartItems->groupBy('product.store_id');
            $createdTransactions = [];

            foreach ($groupedItems as $storeId => $storeItems) {
                // Hitung subtotal untuk toko ini
                $storeSubtotal = $storeItems->sum(function ($item) {
                    return $item->product->current_price * $item->quantity;
                });

                $shippingCost = 15000; // Flat rate per toko
                $totalAmount = $storeSubtotal + $shippingCost;

                // Buat transaksi untuk setiap toko
                $transaction = Transaction::create([
                    'user_id' => $user->id,
                    'store_id' => $storeId,
                    'total_amount' => $totalAmount,
                    'shipping_cost' => $shippingCost,
                    'shipping_address' => $request->shipping_address,
                    'status' => 'pending',
                    'payment_method' => $request->payment_method,
                    'shipping_method' => $request->shipping_method,
                ]);

                // Tambahkan items ke transaksi
                foreach ($storeItems as $item) {
                    TransactionItem::create([
                        'transaction_id' => $transaction->id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price' => $item->product->current_price,
                    ]);

                    // Kurangi stok produk
                    $product = $item->product;
                    $product->decrement('stock', $item->quantity);

                    // Update status produk jika stok habis
                    if ($product->stock <= 0) {
                        $product->update(['status' => 'habis']);
                    }
                }

                $createdTransactions[] = $transaction;
            }

            // Kosongkan keranjang
            Cart::where('user_id', $user->id)->delete();

            DB::commit();

            // Redirect berdasarkan jumlah transaksi
            if (count($createdTransactions) === 1) {
                return redirect()->route('orders.show', $createdTransactions[0]->id)
                    ->with('success', 'Pesanan berhasil dibuat!');
            } else {
                return redirect()->route('orders.index')
                    ->with('success', 'Pesanan berhasil dibuat untuk ' . count($createdTransactions) . ' toko!');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memproses pesanan. Silakan coba lagi.');
        }
    }
}
