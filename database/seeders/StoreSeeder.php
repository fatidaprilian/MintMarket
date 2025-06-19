<?php

namespace Database\Seeders;

use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        $stores = [
            [
                'name' => 'TechnoShop',
                'description' => 'Toko elektronik terpercaya dengan produk berkualitas tinggi dan harga terjangkau',
                'province' => 'DKI Jakarta',
                'city' => 'Jakarta Selatan',
                'address' => 'Jl. Sudirman No. 123'
            ],
            [
                'name' => 'Fashion Central',
                'description' => 'Pusat fashion trendy untuk pria dan wanita dengan koleksi terbaru',
                'province' => 'Jawa Barat',
                'city' => 'Bandung',
                'address' => 'Jl. Braga No. 45'
            ],
            [
                'name' => 'Beauty Corner',
                'description' => 'Produk kecantikan dan perawatan kulit original dan terpercaya',
                'province' => 'DKI Jakarta',
                'city' => 'Jakarta Pusat',
                'address' => 'Jl. Thamrin No. 67'
            ],
            [
                'name' => 'Gadget Zone',
                'description' => 'Handphone, laptop, dan aksesoris teknologi terlengkap',
                'province' => 'Jawa Timur',
                'city' => 'Surabaya',
                'address' => 'Jl. Pemuda No. 89'
            ],
            [
                'name' => 'Sports Arena',
                'description' => 'Perlengkapan olahraga untuk berbagai jenis sport dan aktivitas',
                'province' => 'Jawa Tengah',
                'city' => 'Semarang',
                'address' => 'Jl. Pandanaran No. 234'
            ]
        ];

        // Buat user untuk setiap toko
        foreach ($stores as $storeData) {
            $user = User::create([
                'name' => 'Owner ' . $storeData['name'],
                'email' => Str::slug($storeData['name']) . '@example.com',
                'password' => bcrypt('password'),
                'role' => 'user', // Ubah dari 'seller' ke 'user'
                'email_verified_at' => now(),
            ]);

            Store::create([
                'user_id' => $user->id,
                'name' => $storeData['name'],
                'slug' => Str::slug($storeData['name']),
                'description' => $storeData['description'],
                'province' => $storeData['province'],
                'city' => $storeData['city'],
                'address' => $storeData['address'],
                'is_active' => true,
            ]);
        }
    }
}
