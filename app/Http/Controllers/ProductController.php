<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Store; // <-- TAMBAHKAN IMPORT INI
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Menampilkan daftar semua produk dengan filter dan sorting.
     */
    public function index(Request $request)
    {
        // Ambil daftar lokasi unik untuk dropdown filter
        $locations = Store::select('city')->whereNotNull('city')->distinct()->orderBy('city')->pluck('city');

        $query = Product::with(['store', 'category'])
            ->available();

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by condition
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        // ==========================================================
        // PENAMBAHAN LOGIKA FILTER LOKASI
        // ==========================================================
        if ($request->filled('location')) {
            $query->whereHas('store', function ($q) use ($request) {
                $q->where('city', $request->location);
            });
        }

        // Filter by price range
        $now = now();
        if ($request->filled('min_price')) {
            $minPrice = $request->min_price;
            $query->whereRaw(
                'CASE WHEN flash_sale_price IS NOT NULL AND flash_sale_end_date > ? THEN flash_sale_price ELSE price END >= ?',
                [$now, $minPrice]
            );
        }

        if ($request->filled('max_price')) {
            $maxPrice = $request->max_price;
            $query->whereRaw(
                'CASE WHEN flash_sale_price IS NOT NULL AND flash_sale_end_date > ? THEN flash_sale_price ELSE price END <= ?',
                [$now, $maxPrice]
            );
        }

        // Search by name or description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy(DB::raw('CASE WHEN flash_sale_price IS NOT NULL AND flash_sale_end_date > NOW() THEN flash_sale_price ELSE price END'), 'asc');
                break;
            case 'price_high':
                $query->orderBy(DB::raw('CASE WHEN flash_sale_price IS NOT NULL AND flash_sale_end_date > NOW() THEN flash_sale_price ELSE price END'), 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->latest();
        }

        $products = $query->paginate(12);
        $categories = Category::withCount('products')->get();

        // Kirim data locations ke view
        return view('products.index', compact('products', 'categories', 'request', 'locations'));
    }

    /**
     * Menampilkan halaman detail satu produk.
     */
    public function show(Product $product)
    {
        $product = Product::with(['store.user', 'category'])
            ->where('slug', $product->slug)
            ->firstOrFail();

        $relatedProducts = Product::with(['store', 'category'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->available()
            ->inRandomOrder()
            ->limit(5)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }
}
