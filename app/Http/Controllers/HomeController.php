<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Store;
use Illuminate\Http\Request;

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
            ->take(8)
            ->get();

        // Popular stores
        $popularStores = Store::with('user')
            ->active()
            ->withCount('products')
            ->orderBy('products_count', 'desc')
            ->take(6)
            ->get();

        return view('home', compact('featuredProducts', 'categories', 'popularStores'));
    }
}
