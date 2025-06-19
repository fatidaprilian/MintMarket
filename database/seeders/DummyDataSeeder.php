<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Store; // <-- Tambahkan ini
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat User untuk Penjual dan Pembeli
        // Keduanya sekarang memiliki role 'user'
        $sellerUser = User::create([
            'name' => 'Budi Penjual',
            'email' => 'budi@penjual.com',
            'password' => Hash::make('password'),
            'role' => 'user', // <-- PERUBAHAN
        ]);

        $buyerUser = User::create([
            'name' => 'Siti Pembeli',
            'email' => 'siti@pembeli.com',
            'password' => Hash::make('password'),
            'role' => 'user', // <-- PERUBAHAN
        ]);

        // 2. Buat Toko untuk si Penjual
        $store = Store::create([
            'user_id' => $sellerUser->id,
            'name' => 'Toko Budi Elektronik',
            'slug' => 'toko-budi-elektronik',
            'is_active' => true,
        ]);

        // 3. Buat Kategori
        $category = Category::create([
            'name' => 'Elektronik Bekas',
            'slug' => 'elektronik-bekas',
        ]);

        // 4. Buat Produk oleh Toko
        $product1 = Product::create([
            'store_id' => $store->id, // <-- PERUBAHAN
            'category_id' => $category->id,
            'name' => 'Laptop Gaming Bekas Mulus',
            'slug' => 'laptop-gaming-bekas-mulus',
            'price' => 7500000.00,
            'condition' => 'bekas',
            'status' => 'tersedia',
        ]);

        // 5. Buat Transaksi oleh si Pembeli
        Transaction::create([
            'product_id' => $product1->id,
            'buyer_id' => $buyerUser->id,
            'total_amount' => $product1->price,
            'status' => 'completed',
        ]);
    }
}
