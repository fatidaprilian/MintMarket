<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Menampilkan halaman keranjang belanja.
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk melihat keranjang.');
        }

        $cartItems = $this->getCartItems();
        $total = $this->getCartTotalPrice(); // RENAMED: from getCartTotal to getCartTotalPrice
        $totalItems = $this->getCartTotalItems();

        return view('cart.index', [
            'cartItems' => $cartItems,
            'total' => $total,
            'totalItems' => $totalItems,
        ]);
    }

    /**
     * Get cart items from database for authenticated users
     */
    private function getCartItems()
    {
        $carts = Cart::where('user_id', Auth::id())
            ->with(['product' => function ($query) {
                $query->with('store');
            }])
            ->get();

        return $carts->mapWithKeys(function ($cart) {
            return [$cart->product->id => [
                'cart_id' => $cart->id,
                'name' => $cart->product->name,
                'quantity' => $cart->quantity,
                'price' => $cart->product->price,
                'image' => $cart->product->first_image,
                'slug' => $cart->product->slug ?? $cart->product->id,
                'stock' => $cart->product->stock,
                'condition' => $cart->product->condition,
                'store_name' => $cart->product->store->name ?? 'Unknown Store',
                'subtotal' => $cart->product->price * $cart->quantity,
            ]];
        })->toArray();
    }

    /**
     * Get cart total from database (instance method - RENAMED)
     */
    private function getCartTotalPrice()
    {
        return Cart::where('user_id', Auth::id())
            ->with('product')
            ->get()
            ->sum(function ($cart) {
                return $cart->product->price * $cart->quantity;
            });
    }

    /**
     * Get total items in cart
     */
    private function getCartTotalItems()
    {
        return Cart::where('user_id', Auth::id())->sum('quantity');
    }

    /**
     * Menambahkan produk ke dalam keranjang.
     */
    public function add(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk menambahkan produk ke keranjang.');
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        $product = Product::findOrFail($request->product_id);

        // Cek apakah produk tersedia
        if ($product->status !== 'tersedia') {
            return redirect()->back()->with('error', 'Produk tidak tersedia!');
        }

        // Cek stok
        if ($product->stock < $request->quantity) {
            return redirect()->back()->with('error', 'Stok tidak mencukupi! Stok tersedia: ' . $product->stock);
        }

        // Cek apakah sudah ada di cart
        $existingCart = Cart::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if ($existingCart) {
            $newQuantity = $existingCart->quantity + $request->quantity;

            // Cek apakah total quantity tidak melebihi stok
            if ($newQuantity > $product->stock) {
                return redirect()->back()->with('error', 'Total quantity melebihi stok! Stok tersedia: ' . $product->stock);
            }

            $existingCart->update(['quantity' => $newQuantity]);
        } else {
            // Jika belum ada, tambahkan sebagai item baru
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'quantity' => $request->quantity,
            ]);
        }

        // Flash message untuk notifikasi
        $message = $request->quantity > 1
            ? "{$request->quantity} {$product->name} berhasil ditambahkan ke keranjang!"
            : "{$product->name} berhasil ditambahkan ke keranjang!";

        return redirect()->back()->with('success', $message);
    }

    /**
     * Mengupdate kuantitas produk di keranjang.
     */
    public function update(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $request->validate([
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        $cart = Cart::where('user_id', Auth::id())
            ->where('product_id', $id)
            ->first();

        if (!$cart) {
            return redirect()->route('cart.index')->with('error', 'Produk tidak ditemukan di keranjang!');
        }

        // Validasi dengan produk di database untuk cek stok terbaru
        $product = Product::find($id);
        if ($product && $request->quantity > $product->stock) {
            return redirect()->route('cart.index')->with('error', 'Quantity melebihi stok! Stok tersedia: ' . $product->stock);
        }

        // Update quantity
        $cart->update(['quantity' => $request->quantity]);

        return redirect()->route('cart.index')->with('success', 'Keranjang berhasil diupdate!');
    }

    /**
     * Menghapus produk dari keranjang.
     */
    public function remove($id)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $cart = Cart::where('user_id', Auth::id())
            ->where('product_id', $id)
            ->with('product')
            ->first();

        if ($cart) {
            $productName = $cart->product->name;
            $cart->delete();

            return redirect()->route('cart.index')->with('success', $productName . ' berhasil dihapus dari keranjang!');
        }

        return redirect()->route('cart.index')->with('error', 'Produk tidak ditemukan di keranjang!');
    }

    /**
     * Mengosongkan seluruh keranjang.
     */
    public function clear()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        Cart::where('user_id', Auth::id())->delete();
        return redirect()->route('cart.index')->with('success', 'Keranjang berhasil dikosongkan!');
    }

    /**
     * Mendapatkan jumlah item di keranjang (untuk AJAX/API).
     */
    public function count()
    {
        if (!Auth::check()) {
            return response()->json([
                'count' => 0,
                'items' => 0
            ]);
        }

        $totalItems = Cart::where('user_id', Auth::id())->sum('quantity');
        $itemCount = Cart::where('user_id', Auth::id())->count();

        return response()->json([
            'count' => $totalItems,
            'items' => $itemCount
        ]);
    }

    /**
     * Helper method untuk mendapatkan total harga keranjang (STATIC METHOD).
     */
    public static function getCartTotal()
    {
        if (!Auth::check()) {
            return 0;
        }

        return Cart::where('user_id', Auth::id())
            ->with('product')
            ->get()
            ->sum(function ($cart) {
                return $cart->product->price * $cart->quantity;
            });
    }

    /**
     * Helper method untuk mendapatkan total item di keranjang.
     */
    public static function getCartItemCount()
    {
        if (!Auth::check()) {
            return 0;
        }

        return Cart::where('user_id', Auth::id())->count();
    }

    /**
     * Helper method untuk mendapatkan total quantity di keranjang.
     */
    public static function getCartQuantityCount()
    {
        if (!Auth::check()) {
            return 0;
        }

        return Cart::where('user_id', Auth::id())->sum('quantity');
    }

    /**
     * Sync cart dengan database (untuk memastikan harga dan stok terbaru).
     */
    public function sync()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $carts = Cart::where('user_id', Auth::id())
            ->with('product')
            ->get();

        $hasChanges = false;

        foreach ($carts as $cart) {
            $product = $cart->product;

            if (!$product || $product->status !== 'tersedia') {
                // Produk sudah tidak tersedia, hapus dari keranjang
                $cart->delete();
                $hasChanges = true;
                continue;
            }

            // Jika quantity melebihi stok, sesuaikan
            if ($cart->quantity > $product->stock) {
                if ($product->stock > 0) {
                    $cart->update(['quantity' => $product->stock]);
                } else {
                    $cart->delete();
                }
                $hasChanges = true;
            }
        }

        if ($hasChanges) {
            return redirect()->route('cart.index')->with('info', 'Keranjang telah diperbarui dengan informasi produk terbaru.');
        }

        return redirect()->route('cart.index');
    }
}
