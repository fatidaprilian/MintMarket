<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Buat admin user
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@mintmarket.com',
            'role' => 'admin',
            'password' => bcrypt('123'),
        ]);

        // Jalankan seeder lainnya
        $this->call([
            CategorySeeder::class,
            StoreSeeder::class,
            ProductSeeder::class,
        ]);
    }
}
