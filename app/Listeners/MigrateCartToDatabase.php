<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Cart;
use App\Models\Product;

class MigrateCartToDatabase
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;
        $sessionCart = session()->get('cart', []);

        if (empty($sessionCart)) {
            return;
        }

        foreach ($sessionCart as $productId => $item) {
            $product = Product::find($productId);

            if ($product && $product->status === 'tersedia') {
                // Check if already exists in database cart
                $existingCart = Cart::where('user_id', $user->id)
                    ->where('product_id', $productId)
                    ->first();

                if ($existingCart) {
                    // Update quantity (add session quantity to existing)
                    $newQuantity = min(
                        $existingCart->quantity + $item['quantity'],
                        $product->stock
                    );
                    $existingCart->update(['quantity' => $newQuantity]);
                } else {
                    // Create new cart item
                    Cart::create([
                        'user_id' => $user->id,
                        'product_id' => $productId,
                        'quantity' => min($item['quantity'], $product->stock)
                    ]);
                }
            }
        }

        // Clear session cart after migration
        session()->forget('cart');
    }
}
