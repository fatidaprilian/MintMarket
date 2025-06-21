<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Store;
use Illuminate\Http\Request;
use Carbon\Carbon; // <-- PASTIKAN INI DI-IMPORT

class HomeController extends Controller
{
    public function index()
    {
        // Featured products (produk terbaru atau populer)
        $featuredProducts = Product::with(['store', 'category'])
            ->available()
            ->latest()
            ->take(8)
            ->get();

        // Categories
        $categories = Category::withCount('products')
            ->orderBy('products_count', 'desc')
            ->take(8)
            ->get();

        // Popular stores
        $popularStores = Store::with('user')
            ->active()
            ->withCount('products')
            ->orderBy('products_count', 'desc')
            ->take(6)
            ->get();

        /**
         * ==========================================================
         * LOGIKA BARU: FLASH SALE BERBASIS JADWAL (BATCH)
         * ==========================================================
         */

        // 1. Definisikan jadwal sesi Flash Sale dalam sehari (misal: jam 10:00 dan 20:00)
        $schedule = [12, 20];
        $now = now();
        $activeSessionEnd = null;

        // 2. Tentukan sesi mana yang sedang aktif
        foreach ($schedule as $hour) {
            $sessionStart = $now->copy()->setTime($hour, 0, 0);
            // Sesi berikutnya adalah 14 jam dari awal sesi ini
            $sessionEnd = $sessionStart->copy()->addHours(14);

            if ($now->between($sessionStart, $sessionEnd)) {
                $activeSessionEnd = $sessionEnd;
                break;
            }
        }

        // 3. Ambil produk HANYA jika ada sesi yang aktif
        $flashSaleProducts = collect(); // Defaultnya koleksi kosong
        if ($activeSessionEnd) {
            $flashSaleProducts = Product::with(['store', 'category'])
                ->available()
                ->whereNotNull('flash_sale_price')
                // Kunci: Hanya ambil produk yang waktu berakhirnya TEPAT SAMA dengan akhir sesi aktif
                ->where('flash_sale_end_date', $activeSessionEnd)
                ->orderBy('created_at', 'desc') // Urutkan berdasarkan yang terbaru didaftarkan
                ->take(6)
                ->get();
        }

        return view('home', compact(
            'featuredProducts',
            'categories',
            'popularStores',
            'flashSaleProducts'
        ));
    }
}
