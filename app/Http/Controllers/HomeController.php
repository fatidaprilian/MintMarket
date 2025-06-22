<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Store;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
         * LOGIKA FLASH SALE BERBASIS BATCH SCHEDULE
         * ==========================================================
         */

        // 1. Definisikan jadwal sesi Flash Sale dalam sehari (jam 12:00 dan 22:00)
        $schedule = [9, 22];
        $now = now('Asia/Jakarta'); // Gunakan timezone Jakarta
        $activeSessionEnd = null;
        $flashSaleProducts = collect(); // Default kosong

        // 2. Tentukan sesi mana yang sedang aktif saat ini
        foreach ($schedule as $hour) {
            // Buat waktu mulai sesi untuk hari ini
            $sessionStart = $now->copy()->setTime($hour, 0, 0);
            // Durasi sesi flash sale adalah 14 jam dari waktu mulai
            $sessionEnd = $sessionStart->copy()->addHours(14);

            // Cek apakah waktu sekarang berada dalam rentang sesi ini
            if ($now->between($sessionStart, $sessionEnd)) {
                $activeSessionEnd = $sessionEnd;
                break;
            }
        }

        // 3. Jika ada sesi yang aktif, ambil produk flash sale untuk sesi tersebut
        if ($activeSessionEnd) {
            \Log::info('Active Flash Sale Session Found', [
                'session_end' => $activeSessionEnd->format('Y-m-d H:i:s'),
                'current_time' => $now->format('Y-m-d H:i:s')
            ]);

            // Ambil produk yang terdaftar untuk sesi flash sale yang sedang aktif
            $flashSaleProducts = Product::with(['store', 'category'])
                ->available()
                ->whereNotNull('flash_sale_price')
                ->whereNotNull('flash_sale_end_date')
                // Kunci: Produk yang waktu berakhirnya tepat sama dengan akhir sesi aktif
                ->where('flash_sale_end_date', $activeSessionEnd)
                ->orderBy('created_at', 'desc') // Urutkan berdasarkan yang terbaru didaftarkan
                ->take(6)
                ->get();

            \Log::info('Flash Sale Products Found: ' . $flashSaleProducts->count());
        } else {
            // 4. Jika tidak ada sesi aktif, cari sesi berikutnya untuk informasi
            $nextSession = $this->getNextFlashSaleSession($schedule, $now);

            if ($nextSession) {
                \Log::info('Next Flash Sale Session', [
                    'next_session_start' => $nextSession['start']->format('Y-m-d H:i:s'),
                    'next_session_end' => $nextSession['end']->format('Y-m-d H:i:s')
                ]);
            }
        }

        return view('home', compact(
            'featuredProducts',
            'categories',
            'popularStores',
            'flashSaleProducts'
        ));
    }

    /**
     * Mencari sesi flash sale berikutnya
     */
    private function getNextFlashSaleSession($schedule, $currentTime)
    {
        // Coba cari sesi hari ini yang belum dimulai
        foreach ($schedule as $hour) {
            $sessionStart = $currentTime->copy()->setTime($hour, 0, 0);

            if ($sessionStart->isFuture()) {
                return [
                    'start' => $sessionStart,
                    'end' => $sessionStart->copy()->addHours(14)
                ];
            }
        }

        // Jika tidak ada sesi hari ini, ambil sesi pertama besok
        $tomorrowFirstSession = $currentTime->copy()->addDay()->setTime($schedule[0], 0, 0);
        return [
            'start' => $tomorrowFirstSession,
            'end' => $tomorrowFirstSession->copy()->addHours(14)
        ];
    }
}
