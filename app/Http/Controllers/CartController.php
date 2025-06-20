<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Menampilkan halaman keranjang belanja.
     */
    public function index()
    {
        $cartItems = session()->get('cart', []);
        $total = 0;
        $totalItems = 0;

        foreach ($cartItems as $item) {
            $total += $item['price'] * $item['quantity'];
            $totalItems += $item['quantity'];
        }

        return view('cart.index', [
            'cartItems' => $cartItems,
            'total' => $total,
            'totalItems' => $totalItems,
        ]);
    }

    /**
     * Menambahkan produk ke dalam keranjang.
     */
    public function add(Request $request)
    {
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

        $cart = session()->get('cart', []);
        $productId = $product->id;

        // Cek jika produk sudah ada di keranjang
        if (isset($cart[$productId])) {
            $newQuantity = $cart[$productId]['quantity'] + $request->quantity;

            // Cek apakah total quantity tidak melebihi stok
            if ($newQuantity > $product->stock) {
                return redirect()->back()->with('error', 'Total quantity melebihi stok! Stok tersedia: ' . $product->stock);
            }

            $cart[$productId]['quantity'] = $newQuantity;
        } else {
            // Jika belum ada, tambahkan sebagai item baru
            $cart[$productId] = [
                "name" => $product->name,
                "quantity" => $request->quantity,
                "price" => $product->price,
                "image" => $product->main_image,
                "slug" => $product->slug ?? $product->id,
                "stock" => $product->stock,
                "condition" => $product->condition,
                "store_name" => $product->store->name ?? 'Unknown Store'
            ];
        }

        // Simpan kembali ke session
        session()->put('cart', $cart);

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
        $request->validate([
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        $cart = session()->get('cart', []);

        if (!isset($cart[$id])) {
            return redirect()->route('cart.index')->with('error', 'Produk tidak ditemukan di keranjang!');
        }

        // Validasi dengan produk di database untuk cek stok terbaru
        $product = Product::find($id);
        if ($product && $request->quantity > $product->stock) {
            return redirect()->route('cart.index')->with('error', 'Quantity melebihi stok! Stok tersedia: ' . $product->stock);
        }

        // Update quantity
        $cart[$id]['quantity'] = $request->quantity;

        // Update info terbaru dari database jika produk masih ada
        if ($product) {
            $cart[$id]['price'] = $product->price;
            $cart[$id]['stock'] = $product->stock;
            $cart[$id]['name'] = $product->name;
        }

        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Keranjang berhasil diupdate!');
    }

    /**
     * Menghapus produk dari keranjang.
     */
    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $productName = $cart[$id]['name'];
            unset($cart[$id]);
            session()->put('cart', $cart);

            return redirect()->route('cart.index')->with('success', $productName . ' berhasil dihapus dari keranjang!');
        }

        return redirect()->route('cart.index')->with('error', 'Produk tidak ditemukan di keranjang!');
    }

    /**
     * Mengosongkan seluruh keranjang.
     */
    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('cart.index')->with('success', 'Keranjang berhasil dikosongkan!');
    }

    /**
     * Mendapatkan jumlah item di keranjang (untuk AJAX/API).
     */
    public function count()
    {
        $cartItems = session()->get('cart', []);
        $totalItems = 0;

        foreach ($cartItems as $item) {
            $totalItems += $item['quantity'];
        }

        return response()->json([
            'count' => $totalItems,
            'items' => count($cartItems)
        ]);
    }

    /**
     * Helper method untuk mendapatkan total harga keranjang.
     */
    public static function getCartTotal()
    {
        $cartItems = session()->get('cart', []);
        $total = 0;

        foreach ($cartItems as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return $total;
    }

    /**
     * Helper method untuk mendapatkan total item di keranjang.
     */
    public static function getCartItemCount()
    {
        $cartItems = session()->get('cart', []);
        return count($cartItems);
    }

    /**
     * Helper method untuk mendapatkan total quantity di keranjang.
     */
    public static function getCartQuantityCount()
    {
        $cartItems = session()->get('cart', []);
        $totalQuantity = 0;

        foreach ($cartItems as $item) {
            $totalQuantity += $item['quantity'];
        }

        return $totalQuantity;
    }

    /**
     * Sync cart dengan database (untuk memastikan harga dan stok terbaru).
     */
    public function sync()
    {
        $cart = session()->get('cart', []);
        $updatedCart = [];
        $hasChanges = false;

        foreach ($cart as $id => $item) {
            $product = Product::find($id);

            if ($product && $product->status === 'tersedia') {
                // Update informasi produk dengan data terbaru
                $updatedItem = $item;

                if ($product->price != $item['price']) {
                    $updatedItem['price'] = $product->price;
                    $hasChanges = true;
                }

                if ($product->stock != $item['stock']) {
                    $updatedItem['stock'] = $product->stock;
                    $hasChanges = true;
                }

                if ($product->name != $item['name']) {
                    $updatedItem['name'] = $product->name;
                    $hasChanges = true;
                }

                // Jika quantity melebihi stok, sesuaikan
                if ($item['quantity'] > $product->stock) {
                    $updatedItem['quantity'] = $product->stock;
                    $hasChanges = true;
                }

                $updatedCart[$id] = $updatedItem;
            } else {
                // Produk sudah tidak tersedia, hapus dari keranjang
                $hasChanges = true;
            }
        }

        if ($hasChanges) {
            session()->put('cart', $updatedCart);
            return redirect()->route('cart.index')->with('info', 'Keranjang telah diperbarui dengan informasi produk terbaru.');
        }

        return redirect()->route('cart.index');
    }
}
