<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Store;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Elektronik
            [
                'category' => 'Elektronik',
                'name' => 'Samsung Galaxy S24 Ultra 256GB',
                'description' => 'Smartphone flagship terbaru dari Samsung dengan kamera 200MP dan S Pen yang canggih. Kondisi baru dalam box dengan garansi resmi.',
                'price' => 18999000,
                'condition' => 'Baru',
                'status' => 'tersedia'
            ],
            [
                'category' => 'Elektronik',
                'name' => 'iPhone 15 Pro 128GB',
                'description' => 'iPhone terbaru dengan chip A17 Pro dan kamera titanium yang premium. Lengkap dengan charger dan earphone original.',
                'price' => 19999000,
                'condition' => 'Baru',
                'status' => 'tersedia'
            ],
            [
                'category' => 'Komputer & Laptop',
                'name' => 'MacBook Air M2 13-inch',
                'description' => 'Laptop tipis dan ringan dengan performa tinggi untuk produktivitas sehari-hari. RAM 8GB SSD 256GB.',
                'price' => 17999000,
                'condition' => 'Baru',
                'status' => 'tersedia'
            ],
            [
                'category' => 'Komputer & Laptop',
                'name' => 'ASUS ROG Strix G15',
                'description' => 'Gaming laptop dengan AMD Ryzen 7 dan RTX 3060 untuk gaming dan content creation.',
                'price' => 15999000,
                'condition' => 'Bekas',
                'status' => 'tersedia'
            ],

            // Fashion
            [
                'category' => 'Fashion Pria',
                'name' => 'Kemeja Flanel Premium',
                'description' => 'Kemeja flanel berkualitas tinggi dengan bahan cotton yang nyaman dan design casual.',
                'price' => 149000,
                'condition' => 'Baru',
                'status' => 'tersedia'
            ],
            [
                'category' => 'Fashion Wanita',
                'name' => 'Dress Midi Elegant',
                'description' => 'Dress midi dengan design elegant untuk acara formal maupun casual. Bahan premium dan nyaman dipakai.',
                'price' => 299000,
                'condition' => 'Baru',
                'status' => 'tersedia'
            ],

            // Kesehatan & Kecantikan
            [
                'category' => 'Kesehatan & Kecantikan',
                'name' => 'Serum Vitamin C Original',
                'description' => 'Serum wajah dengan kandungan Vitamin C 20% untuk mencerahkan dan melembabkan kulit.',
                'price' => 89000,
                'condition' => 'Baru',
                'status' => 'tersedia'
            ],
            [
                'category' => 'Kesehatan & Kecantikan',
                'name' => 'Skincare Set Complete',
                'description' => 'Set lengkap perawatan wajah dengan cleanser, toner, serum, dan moisturizer.',
                'price' => 259000,
                'condition' => 'Baru',
                'status' => 'tersedia'
            ],

            // Olahraga
            [
                'category' => 'Olahraga',
                'name' => 'Sepatu Running Nike Air Max',
                'description' => 'Sepatu lari dengan teknologi Air Max untuk kenyamanan maksimal saat berolahraga.',
                'price' => 1299000,
                'condition' => 'Baru',
                'status' => 'tersedia'
            ],
            [
                'category' => 'Olahraga',
                'name' => 'Matras Yoga Premium',
                'description' => 'Matras yoga anti-slip dengan ketebalan 6mm, cocok untuk yoga dan pilates.',
                'price' => 199000,
                'condition' => 'Baru',
                'status' => 'tersedia'
            ],

            // Hobi & Koleksi
            [
                'category' => 'Hobi & Koleksi',
                'name' => 'Kamera Polaroid Instax Mini',
                'description' => 'Kamera instant untuk mengabadikan momen spesial dengan hasil foto langsung tercetak.',
                'price' => 899000,
                'condition' => 'Baru',
                'status' => 'tersedia'
            ],
            [
                'category' => 'Buku & Majalah',
                'name' => 'Novel Best Seller Collection',
                'description' => 'Koleksi novel best seller dalam dan luar negeri. Kondisi seperti baru.',
                'price' => 150000,
                'condition' => 'Bekas',
                'status' => 'tersedia'
            ]
        ];

        $stores = Store::all();
        $categories = Category::all()->keyBy('name');

        foreach ($products as $productData) {
            $category = $categories[$productData['category']] ?? $categories->first();
            $store = $stores->random();

            Product::create([
                'store_id' => $store->id,
                'category_id' => $category->id,
                'name' => $productData['name'],
                'slug' => Str::slug($productData['name']) . '-' . Str::random(5),
                'description' => $productData['description'],
                'price' => $productData['price'],
                'condition' => $productData['condition'],
                'status' => $productData['status'],
                'image' => null, // Kita set null dulu, nanti bisa upload image
            ]);
        }
    }
}
