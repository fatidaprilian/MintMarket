<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\Store; // Pastikan model Store di-import
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    /**
     * Menampilkan halaman checkout.
     */
    public function index()
    {
        $user = Auth::user();

        // Cek apakah ini adalah pembelian langsung
        $isBuyNow = session('buy_now', false);
        Log::info('CheckoutController@index: isBuyNow = ' . ($isBuyNow ? 'true' : 'false'));

        if ($isBuyNow) {
            // Ambil item buy now dari keranjang
            $cartItems = Cart::where('user_id', $user->id)
                ->where('is_buy_now', true)
                ->with('product.store')
                ->get();
            Log::info('CheckoutController@index: Fetched ' . $cartItems->count() . ' buy now cart items.');
        } else {
            // Ambil item normal dari keranjang
            $cartItems = Cart::where('user_id', $user->id)
                ->where(function ($query) {
                    $query->where('is_buy_now', false)
                        ->orWhereNull('is_buy_now');
                })
                ->with('product.store')
                ->get();
            Log::info('CheckoutController@index: Fetched ' . $cartItems->count() . ' regular cart items.');
        }

        // Validasi keranjang
        $hasChanges = false;
        foreach ($cartItems as $key => $cart) {
            $product = $cart->product;

            if (!$product || $product->status !== 'tersedia') {
                Log::warning('CheckoutController@index: Product ' . ($product ? $product->name : 'Unknown') . ' not available or found. Deleting cart item: ' . $cart->id);
                $cart->delete();
                $cartItems->forget($key);
                $hasChanges = true;
                continue;
            }

            if ($cart->quantity > $product->stock) {
                Log::warning('CheckoutController@index: Cart quantity (' . $cart->quantity . ') for product ' . $product->name . ' exceeds stock (' . $product->stock . ').');
                if ($product->stock > 0) {
                    $cart->update(['quantity' => $product->stock]);
                    $cart->quantity = $product->stock;
                    Log::info('CheckoutController@index: Cart quantity updated to product stock.');
                } else {
                    $cart->delete();
                    $cartItems->forget($key);
                    Log::info('CheckoutController@index: Product stock is zero, deleting cart item.');
                }
                $hasChanges = true;
            }
        }

        if ($hasChanges) {
            Log::info('CheckoutController@index: Cart changes detected, redirecting to cart.index.');
            return redirect()->route('cart.index')->with('info', 'Keranjang Anda telah disesuaikan dengan stok dan ketersediaan produk terbaru.');
        }

        if ($cartItems->isEmpty()) {
            if ($isBuyNow) {
                session()->forget('buy_now');
                Log::info('CheckoutController@index: Buy now session cleared as cart is empty.');
            }
            Log::warning('CheckoutController@index: Cart is empty, redirecting to cart.index.');
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

        Log::info('CheckoutController@index: Displaying checkout page with ' . count($groupedItems) . ' stores and grand total: ' . $grandTotal);
        return view('checkout.index', [
            'cartItems' => $cartItems,
            'groupedItems' => $groupedItems,
            'storeSubtotals' => $storeSubtotals,
            'subtotal' => $totalSubtotal,
            'shippingCost' => $totalShippingCost,
            'total' => $grandTotal,
            'user' => $user,
            'isBuyNow' => $isBuyNow,
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
            Log::warning('CheckoutController@buyNow: Product ' . $product->name . ' not available or stock insufficient. Status: ' . $product->status . ', Stock: ' . $product->stock . ', Requested: ' . $quantity);
            return redirect()->route('products.show', $product->slug)->with('error', 'Produk tidak tersedia atau stok tidak mencukupi.');
        }

        try {
            DB::beginTransaction();
            Log::info('CheckoutController@buyNow: DB transaction started for buy now.');

            Cart::where('user_id', $user->id)
                ->where('is_buy_now', true)
                ->delete();
            Log::info('CheckoutController@buyNow: Deleted previous buy now cart items for user ' . $user->id);

            $cartItem = Cart::create([
                'user_id' => $user->id,
                'product_id' => $productId,
                'quantity' => $quantity,
                'is_buy_now' => true
            ]);
            Log::info('CheckoutController@buyNow: Created new buy now cart item: ' . $cartItem->id);

            DB::commit();
            Log::info('CheckoutController@buyNow: DB transaction committed for buy now.');

            session(['buy_now' => true]);
            Log::info('CheckoutController@buyNow: Session "buy_now" set to true.');

            return redirect()->route('checkout.index');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('CheckoutController@buyNow: Error processing buy now: ' . $e->getMessage());
            Log::error('CheckoutController@buyNow: Stack trace: ' . $e->getTraceAsString());
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
            Log::warning('CheckoutController@process: Validation failed: ' . $validator->errors()->toJson());
            return back()->withErrors($validator)->withInput()->with('error', 'Silakan lengkapi semua field yang diperlukan.');
        }

        $user = Auth::user();

        try {
            DB::beginTransaction();
            Log::info('CheckoutController@process: Starting checkout process for user: ' . $user->id);

            if ($request->has('save_address') && $request->save_address) {
                $user->update([
                    'address' => $request->shipping_address,
                    'city' => $request->city,
                    'postal_code' => $request->postal_code,
                    'phone' => $request->phone,
                ]);
                Log::info('CheckoutController@process: User address updated for user: ' . $user->id);
            }

            $fullAddress = $request->shipping_address . "\nKota: " . $request->city . "\nKode Pos: " . $request->postal_code . "\nTelepon: " . $request->phone;

            $isBuyNow = session('buy_now', false);
            Log::info('CheckoutController@process: Is buy now: ' . ($isBuyNow ? 'true' : 'false') . ' for user: ' . $user->id);

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

            Log::info('CheckoutController@process: Cart items count fetched: ' . $cartItems->count() . ' for user: ' . $user->id);

            if ($cartItems->isEmpty()) {
                DB::rollBack();
                if ($isBuyNow) {
                    session()->forget('buy_now');
                }
                Log::warning('CheckoutController@process: Cart is empty during process for user: ' . $user->id . '. Rolling back.');
                return redirect()->route('cart.index')->with('error', 'Checkout gagal karena keranjang Anda kosong.');
            }

            foreach ($cartItems as $item) {
                if (!$item->product || $item->product->status !== 'tersedia') {
                    DB::rollBack();
                    $productName = $item->product ? $item->product->name : 'Unknown';
                    Log::error('CheckoutController@process: Product "' . $productName . '" not available for user: ' . $user->id . '. Rolling back.');
                    return back()->with('error', 'Produk "' . ($item->product ? $item->product->name : 'Unknown') . '" tidak tersedia.');
                }
                if ($item->quantity > $item->product->stock) {
                    DB::rollBack();
                    Log::error('CheckoutController@process: Stock insufficient for product "' . $item->product->name . '". Requested: ' . $item->quantity . ', Available: ' . $item->product->stock . '. Rolling back.');
                    return back()->with('error', 'Stok produk "' . $item->product->name . '" tidak mencukupi. Stok tersedia: ' . $item->product->stock);
                }
            }

            $groupedItems = $cartItems->groupBy('product.store_id');
            $createdTransactions = [];
            Log::info('CheckoutController@process: Processing ' . count($groupedItems) . ' stores for user: ' . $user->id);

            foreach ($groupedItems as $storeId => $storeItems) {
                $store = Store::find($storeId);
                if (!$store) {
                    DB::rollBack();
                    Log::error('CheckoutController@process: Store not found for ID: ' . $storeId . '. Rolling back.');
                    return back()->with('error', 'Toko tidak ditemukan untuk salah satu produk.');
                }

                $storeSubtotal = $storeItems->sum(fn($item) => $item->product->current_price * $item->quantity);
                $shippingCost = 15000;
                $totalAmount = $storeSubtotal + $shippingCost;

                do {
                    $transactionCode = 'TRX-' . strtoupper(Str::random(8));
                } while (Transaction::where('transaction_code', $transactionCode)->exists());

                // == REVISI DI SINI ==
                // Tentukan status awal berdasarkan pengaturan toko
                $initialStatus = $store->auto_process_orders ? 'processing' : 'pending';
                Log::info('CheckoutController@process: Store ' . $store->name . ' auto_process_orders is ' . ($store->auto_process_orders ? 'true' : 'false') . '. Initial status set to: ' . $initialStatus);

                $transaction = Transaction::create([
                    'user_id' => $user->id,
                    'store_id' => $storeId,
                    'total_amount' => $totalAmount,
                    'shipping_cost' => $shippingCost,
                    'shipping_address' => $fullAddress,
                    'status' => $initialStatus, // Menggunakan status yang sudah ditentukan
                    'payment_method' => $request->payment_method,
                    'shipping_method' => $request->shipping_method,
                    'transaction_code' => $transactionCode,
                ]);
                Log::info('CheckoutController@process: Transaction created with ID: ' . $transaction->id . ' for user: ' . $user->id);

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
                    Log::info('CheckoutController@process: Product stock updated: ' . $product->name . ' - New stock: ' . $newStock . ' for user: ' . $user->id);
                }
                $createdTransactions[] = $transaction;
            }

            $cartItemIds = $cartItems->pluck('id');
            Cart::whereIn('id', $cartItemIds)->delete();
            Log::info('CheckoutController@process: Deleted ' . count($cartItemIds) . ' cart items for user: ' . $user->id);

            session()->forget('buy_now');
            Log::info('CheckoutController@process: Session "buy_now" cleared for user: ' . $user->id);

            DB::commit();
            Log::info('CheckoutController@process: Checkout completed successfully for user: ' . $user->id . '. Transactions created: ' . count($createdTransactions));

            if (count($createdTransactions) === 1) {
                return redirect()->route('orders.show', $createdTransactions[0]->id)
                    ->with('success', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran untuk melanjutkan proses.');
            } else {
                return redirect()->route('orders.index')
                    ->with('success', 'Pesanan berhasil dibuat untuk ' . count($createdTransactions) . ' toko! Silakan lakukan pembayaran untuk melanjutkan proses.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CheckoutController@process: Checkout error for user ' . $user->id . ': ' . $e->getMessage());
            Log::error('CheckoutController@process: Stack trace: ' . $e->getTraceAsString());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memproses pesanan. Silakan coba lagi. Error: ' . $e->getMessage());
        }
    }
}
