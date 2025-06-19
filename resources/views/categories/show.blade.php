@extends('layouts.app')

@section('title', $category->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('home') }}" class="text-gray-700 hover:text-sage-600">Beranda</a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <a href="{{ route('categories.index') }}" class="ml-1 text-gray-700 hover:text-sage-600 md:ml-2">Kategori</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-gray-500 md:ml-2">{{ $category->name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Category Header -->
    <div class="bg-gradient-to-r from-sage-600 to-sage-700 rounded-lg p-8 mb-8 text-white">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold mb-2">{{ $category->name }}</h1>
                <p class="text-sage-100">
                    {{ $products->total() }} produk tersedia dalam kategori ini
                </p>
            </div>
        </div>
    </div>

    <!-- Sort & View Options -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <p class="text-gray-600">
                Menampilkan {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} 
                dari {{ $products->total() }} produk
            </p>
        </div>
        
        <form action="{{ route('categories.show', $category) }}" method="GET" class="flex items-center gap-2">
            <label class="text-sm text-gray-600">Urutkan:</label>
            <select name="sort" onchange="this.form.submit()" 
                    class="px-3 py-2 border border-gray-300 rounded-md focus:ring-sage-500 focus:border-sage-500">
                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga Terendah</option>
                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga Tertinggi</option>
                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nama A-Z</option>
            </select>
        </form>
    </div>

    <!-- Products Grid -->
    @if($products->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
            @foreach($products as $product)
                <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                    <a href="{{ route('products.show', $product) }}">
                        <div class="aspect-square bg-gray-200 relative overflow-hidden">
                            @if($product->main_image)
                                <img src="{{ asset('storage/' . $product->main_image) }}" 
                                     alt="{{ $product->name }}" 
                                     class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            @endif
                            
                            <!-- Condition Badge -->
                            <div class="absolute top-2 left-2">
                                <span class="bg-white/90 text-gray-700 px-2 py-1 rounded text-xs font-medium">
                                    {{ $product->condition }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="p-4">
                            <h3 class="font-medium text-gray-900 mb-2 line-clamp-2 leading-5">{{ $product->name }}</h3>
                            <p class="text-lg font-bold text-sage-600 mb-2">{{ $product->formatted_price }}</p>
                            <div class="flex items-center justify-between text-sm text-gray-500">
                                <span class="truncate">{{ $product->store->name }}</span>
                                <span class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $product->category->name }}</span>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="flex justify-center">
            {{ $products->withQueryString()->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <div class="max-w-md mx-auto">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Produk</h3>
                <p class="text-gray-500 mb-4">Belum ada produk dalam kategori {{ $category->name }}</p>
                <a href="{{ route('products.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-sage-600 text-white rounded-md hover:bg-sage-700 transition-colors">
                    Lihat Semua Produk
                </a>
            </div>
        </div>
    @endif
</div>
@endsection