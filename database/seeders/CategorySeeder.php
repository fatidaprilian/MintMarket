<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Elektronik',
            'Fashion Pria',
            'Fashion Wanita',
            'Kesehatan & Kecantikan',
            'Hobi & Koleksi',
            'Rumah & Taman',
            'Olahraga',
            'Otomotif',
            'Handphone & Aksesoris',
            'Komputer & Laptop',
            'Buku & Majalah',
            'Mainan & Games'
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category,
                'slug' => Str::slug($category),
            ]);
        }
    }
}
