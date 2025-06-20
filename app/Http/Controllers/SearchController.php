<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Store;
use App\Models\Category;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q', '');
        $category = $request->get('category');
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');
        $sort = $request->get('sort', 'relevance');

        if (empty($query)) {
            return redirect()->route('products.index');
        }

        // Build search query
        $products = Product::where('status', 'tersedia')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhereHas('category', function ($cat) use ($query) {
                        $cat->where('name', 'like', "%{$query}%");
                    })
                    ->orWhereHas('store', function ($store) use ($query) {
                        $store->where('name', 'like', "%{$query}%");
                    });
            })
            ->with(['store', 'category']);

        // Apply filters
        if ($category) {
            $products->where('category_id', $category);
        }

        if ($minPrice) {
            $products->where('price', '>=', $minPrice);
        }

        if ($maxPrice) {
            $products->where('price', '<=', $maxPrice);
        }

        // Apply sorting
        switch ($sort) {
            case 'price_low':
                $products->orderBy('price', 'asc');
                break;
            case 'price_high':
                $products->orderBy('price', 'desc');
                break;
            case 'newest':
                $products->orderBy('created_at', 'desc');
                break;
            case 'popular':
                $products->orderBy('created_at', 'desc'); // fallback
                break;
            default: // relevance
                $products->orderBy('created_at', 'desc');
                break;
        }

        $results = $products->paginate(20)->withQueryString();

        // Get categories for filter
        $categories = Category::orderBy('name')->get();

        return view('search.index', [
            'query' => $query,
            'products' => $results,
            'total' => $results->total(),
            'categories' => $categories,
            'currentCategory' => $category,
            'currentSort' => $sort,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice
        ]);
    }
}
