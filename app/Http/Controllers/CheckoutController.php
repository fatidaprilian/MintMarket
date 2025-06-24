<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http; // <-- Perubahan: Ditambahkan
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    /**
     * Menampilkan halaman checkout.
     */
    public function index()
    {
        $user = Auth::user()->load('wallet');

        // Cek apakah ini adalah pembelian langsung
        $isBuyNow = session('buy_now', false);

        if ($isBuyNow) {
            $cartItems = Cart::where('user_id', $user->id)
                ->where('is_buy_now', true)
                ->with('product.store')
                ->get();
        } else {
            $cartItems = Cart::where('user_id', $user->id)
                ->where(function ($query) {
                    $query->where('is_buy_now', false)
                        ->orWhereNull('is_buy_now');
                })
                ->with('product.store')
                ->get();
        }

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
            if ($isBuyNow) {
                session()->forget('buy_now');
            }
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

        // --- PERUBAHAN DIMULAI DI SINI ---
        // Mengambil daftar kota/kabupaten dari API
        $citiesResponse = Http::withoutVerifying()->get('https://api.nusakita.yuefii.site/v2/kab-kota?pagination=false');
        $cities = [];
        if ($citiesResponse->successful() && isset($citiesResponse->json()['data'])) {
            $cities = $citiesResponse->json()['data'];
        }
        // --- PERUBAHAN SELESAI ---

        return view('checkout.index', [
            'cartItems' => $cartItems,
            'groupedItems' => $groupedItems,
            'storeSubtotals' => $storeSubtotals,
            'subtotal' => $totalSubtotal,
            'shippingCost' => $totalShippingCost,
            'total' => $grandTotal,
            'user' => $user,
            'isBuyNow' => $isBuyNow,
            'cities' => $cities, // <-- Perubahan: Mengirim data kota ke view
        ]);
    }

    /**
     * Memproses pesanan dari halaman produk (Pesan Sekarang).
     */
    public function buyNow(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $productId = $request->product_id;
        $quantity = $request->quantity;
        $user = Auth::user();

        $product = Product::with('store')->findOrFail($productId);

        if ($product->status !== 'tersedia' || $product->stock < $quantity) {
            return redirect()->route('products.show', $product->slug)->with('error', 'Produk tidak tersedia atau stok tidak mencukupi.');
        }

        try {
            DB::beginTransaction();

            Cart::where('user_id', $user->id)
                ->where('is_buy_now', true)
                ->delete();

            Cart::create([
                'user_id' => $user->id,
                'product_id' => $productId,
                'quantity' => $quantity,
                'is_buy_now' => true
            ]);

            DB::commit();

            session(['buy_now' => true]);

            return redirect()->route('checkout.index');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('products.show', $product->slug)
                ->with('error', 'Terjadi kesalahan saat memproses pesanan: ' . $e->getMessage());
        }
    }

    /**
     * Memproses pesanan dari form checkout.
     */
    public function process(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'shipping_address' => 'required|string|max:1000',
            'shipping_method' => 'required|string',
            'payment_method' => 'required|string',
            'save_address' => 'boolean',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'phone' => 'required|string|max:15',
        ], [
            'shipping_address.required' => 'Alamat pengiriman harus diisi',
            'shipping_method.required' => 'Metode pengiriman harus dipilih',
            'payment_method.required' => 'Metode pembayaran harus dipilih',
            'city.required' => 'Kota harus diisi',
            'postal_code.required' => 'Kode pos harus diisi',
            'phone.required' => 'Nomor telepon harus diisi',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Silakan lengkapi semua field yang diperlukan.');
        }

        $user = Auth::user()->load('wallet');

        try {
            DB::beginTransaction();

            if ($request->has('save_address') && $request->save_address) {
                $user->update([
                    'address' => $request->shipping_address,
                    'city' => $request->city,
                    'postal_code' => $request->postal_code,
                    'phone' => $request->phone,
                ]);
            }

            $fullAddress = $request->shipping_address . "\nKota: " . $request->city . "\nKode Pos: " . $request->postal_code . "\nTelepon: " . $request->phone;

            $isBuyNow = session('buy_now', false);

            if ($isBuyNow) {
                $cartItems = Cart::where('user_id', $user->id)
                    ->where('is_buy_now', true)
                    ->with('product.store')
                    ->get();
            } else {
                $cartItems = Cart::where('user_id', $user->id)
                    ->where(function ($query) {
                        $query->where('is_buy_now', false)
                            ->orWhereNull('is_buy_now');
                    })
                    ->with('product.store')
                    ->get();
            }

            if ($cartItems->isEmpty()) {
                DB::rollBack();
                if ($isBuyNow) {
                    session()->forget('buy_now');
                }
                return redirect()->route('cart.index')->with('error', 'Checkout gagal karena keranjang Anda kosong.');
            }

            foreach ($cartItems as $item) {
                if (!$item->product || $item->product->status !== 'tersedia') {
                    DB::rollBack();
                    return back()->with('error', 'Produk "' . ($item->product ? $item->product->name : 'Unknown') . '" tidak tersedia.');
                }
                if ($item->quantity > $item->product->stock) {
                    DB::rollBack();
                    return back()->with('error', 'Stok produk "' . $item->product->name . '" tidak mencukupi. Stok tersedia: ' . $item->product->stock);
                }
            }

            $groupedItems = $cartItems->groupBy('product.store_id');
            $storeSubtotals = [];
            $totalSubtotal = 0;

            foreach ($groupedItems as $storeId => $storeItems) {
                $storeSubtotal = $storeItems->sum(function ($item) {
                    return $item->product->current_price * $item->quantity;
                });
                $storeSubtotals[$storeId] = $storeSubtotal;
                $totalSubtotal += $storeSubtotal;
            }

            $shippingCostPerStore = 15000;
            $totalShippingCost = count($groupedItems) * $shippingCostPerStore;
            $grandTotal = $totalSubtotal + $totalShippingCost;

            // Validasi saldo jika pilih "saldo"
            if ($request->payment_method === 'saldo') {
                if ($user->wallet->balance < $grandTotal) {
                    DB::rollBack();
                    return back()->withInput()->with('error', 'Saldo dompet Anda tidak cukup untuk membayar pesanan ini.');
                }
            }

            $createdTransactions = [];

            foreach ($groupedItems as $storeId => $storeItems) {
                $store = Store::find($storeId);
                if (!$store) {
                    DB::rollBack();
                    return back()->with('error', 'Toko tidak ditemukan untuk salah satu produk.');
                }

                $storeSubtotal = $storeSubtotals[$storeId];
                $shippingCost = $shippingCostPerStore;
                $totalAmount = $storeSubtotal + $shippingCost;

                do {
                    $transactionCode = 'TRX-' . strtoupper(Str::random(8));
                } while (Transaction::where('transaction_code', $transactionCode)->exists());

                // Status awal
                // Status awal
                $initialStatus = $store->auto_process_orders ? 'processing' : 'pending'; // Default status

                if ($request->payment_method === 'saldo') {
                    $initialStatus = 'paid';
                } elseif (in_array($request->payment_method, ['cod', 'bank_transfer', 'e_wallet', 'virtual_account'])) {
                    $initialStatus = 'pending';
                }
                // If it's none of the above, it will retain the default status set initially.

                $transaction = Transaction::create([
                    'user_id' => $user->id,
                    'store_id' => $storeId,
                    'total_amount' => $totalAmount,
                    'shipping_cost' => $shippingCost,
                    'shipping_address' => $fullAddress,
                    'status' => $initialStatus,
                    'payment_method' => $request->payment_method,
                    'shipping_method' => $request->shipping_method,
                    'transaction_code' => $transactionCode,
                ]);

                foreach ($storeItems as $item) {
                    TransactionItem::create([
                        'transaction_id' => $transaction->id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price' => $item->product->current_price,
                    ]);

                    $product = $item->product;
                    $newStock = $product->stock - $item->quantity;
                    $product->update(['stock' => $newStock]);
                    if ($newStock <= 0) {
                        $product->update(['status' => 'habis']);
                    }
                }
                $createdTransactions[] = $transaction;
            }

            $cartItemIds = $cartItems->pluck('id');
            Cart::whereIn('id', $cartItemIds)->delete();
            session()->forget('buy_now');

            // Jika bayar pakai saldo, potong saldo user dan buat log transaksi wallet
            if ($request->payment_method === 'saldo') {
                $user->wallet->balance -= $grandTotal;
                $user->wallet->save();

                if (method_exists($user->wallet, 'transactions')) {
                    foreach ($createdTransactions as $tr) {
                        $user->wallet->transactions()->create([
                            'amount' => -$tr->total_amount,
                            'type' => 'debit',
                            'description' => 'Pembayaran pesanan #' . $tr->transaction_code,
                            'running_balance' => $user->wallet->balance,
                            'reference_type' => Transaction::class,
                            'reference_id' => $tr->id,
                        ]);
                    }
                }
            }

            DB::commit();

            if (count($createdTransactions) === 1) {
                return redirect()->route('orders.show', $createdTransactions[0]->id)
                    ->with('success', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran untuk melanjutkan proses.');
            } else {
                return redirect()->route('orders.index')
                    ->with('success', 'Pesanan berhasil dibuat untuk ' . count($createdTransactions) . ' toko! Silakan lakukan pembayaran untuk melanjutkan proses.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memproses pesanan. Silakan coba lagi. Error: ' . $e->getMessage());
        }
    }
}
