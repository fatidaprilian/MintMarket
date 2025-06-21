<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cart as CartModel;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class Cart extends Component
{
    public $cartItems = [];
    public $total = 0;
    public $totalItems = 0;

    public function mount()
    {
        $this->loadCartData();
    }

    public function loadCartData()
    {
        if (!Auth::check()) {
            $this->cartItems = [];
            $this->total = 0;
            $this->totalItems = 0;
            return;
        }

        $userId = Auth::id();
        $carts = CartModel::where('user_id', $userId)
            ->with(['product' => function ($query) {
                $query->with('store');
            }])
            ->get();

        $this->cartItems = $carts->map(function ($cart) {
            // Jika karena satu dan lain hal produk tidak ditemukan, lewati.
            if (!$cart->product) {
                return null;
            }

            // <-- PERUBAHAN LOGIKA UTAMA ADA DI SINI -->
            $currentPrice = $cart->product->current_price; // Menggunakan accessor dari Model Product

            return [
                'id' => $cart->product->id,
                'cart_id' => $cart->id,
                'name' => $cart->product->name,
                'price' => $currentPrice, // <-- Harga disesuaikan
                'quantity' => $cart->quantity,
                'stock' => $cart->product->stock,
                'image' => $cart->product->main_image,
                'store_name' => $cart->product->store->name ?? 'Tidak Diketahui',
                'subtotal' => $currentPrice * $cart->quantity, // <-- Subtotal juga disesuaikan
            ];
        })->filter()->keyBy('id')->toArray(); // ->filter() untuk menghapus item null

        $this->calculateTotals();
    }

    private function calculateTotals()
    {
        $this->total = collect($this->cartItems)->sum('subtotal');
        $this->totalItems = collect($this->cartItems)->sum('quantity');
    }

    public function incrementQuantity($productId)
    {
        if (!Auth::check()) return;

        $currentQuantity = $this->cartItems[$productId]['quantity'] ?? 0;
        $stock = $this->cartItems[$productId]['stock'] ?? 0;

        if ($currentQuantity >= $stock) return;

        $this->updateQuantity($productId, $currentQuantity + 1);
    }

    public function decrementQuantity($productId)
    {
        if (!Auth::check()) return;

        $currentQuantity = $this->cartItems[$productId]['quantity'] ?? 0;

        if ($currentQuantity <= 1) {
            $this->remove($productId);
        } else {
            $this->updateQuantity($productId, $currentQuantity - 1);
        }
    }

    public function updateQuantity($productId, $quantity)
    {
        if (!Auth::check()) return;

        $quantity = (int) $quantity;

        if ($quantity < 1) {
            $this->remove($productId);
            return;
        }

        $userId = Auth::id();
        $product = Product::find($productId);

        if (!$product || $product->status !== 'tersedia') {
            $this->remove($productId);
            return;
        }

        if ($quantity > $product->stock) {
            $quantity = $product->stock;
        }

        // Update database
        CartModel::where('user_id', $userId)
            ->where('product_id', $productId)
            ->update(['quantity' => $quantity]);

        // Reload data
        $this->loadCartData();

        // Dispatch event
        $this->dispatch('cartUpdated');
    }

    public function remove($productId)
    {
        if (!Auth::check()) return;

        $userId = Auth::id();
        CartModel::where('user_id', $userId)
            ->where('product_id', $productId)
            ->delete();

        $this->loadCartData();
        $this->dispatch('cartUpdated');
    }

    public function clearCart()
    {
        if (!Auth::check()) return;

        $userId = Auth::id();
        CartModel::where('user_id', $userId)->delete();

        $this->loadCartData();
        $this->dispatch('cartUpdated');
    }

    public function render()
    {
        return view('livewire.cart');
    }
}
