@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<!-- Hero Section dengan Sage Gradient -->
<section class="bg-gradient-to-r from-sage-600 to-sage-700 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <h1 class="text-4xl md:text-5xl font-bold mb-6">
                    Jual Beli Online <br>
                    <span class="text-sage-200">Mudah & Terpercaya</span>
                </h1>
                <p class="text-xl text-sage-100 mb-8">
                    Temukan produk favorit Anda atau mulai berjualan dengan mudah di MintMarket
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('products.index') }}" 
                       class="bg-white text-sage-600 px-8 py-3 rounded-lg font-semibold hover:bg-sage-50 transition-colors text-center">
                        Mulai Belanja
                    </a>
                    @auth
                        @if(!auth()->user()->hasStore())
                            <a href="{{ route('store.create') }}" 
                               class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-sage-600 transition-colors text-center">
                                Buka Toko
                            </a>
                        @endif
                    @else
                        <a href="{{ route('register') }}" 
                           class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-sage-600 transition-colors text-center">
                            Daftar Sekarang
                        </a>
                    @endauth
                </div>
            </div>
            <div class="hidden lg:block">
                <div class="relative">
                    <div class="w-full h-96 bg-white/10 rounded-2xl backdrop-blur-sm p-8">
                        <div class="h-full flex items-center justify-center text-white/50">
                            <!-- Placeholder untuk hero image -->
                            <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Kategori Populer</h2>
            <p class="text-gray-600">Temukan produk sesuai kebutuhan Anda</p>
        </div>
        
        @if($categories && $categories->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-6">
                @foreach($categories as $category)
                    <a href="{{ route('categories.show', $category) }}" 
                       class="group text-center p-4 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="w-16 h-16 bg-gradient-to-br from-sage-100 to-sage-200 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:from-sage-200 group-hover:to-sage-300 transition-colors">
                            <svg class="w-8 h-8 text-sage-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path>
                            </svg>
                        </div>
                        <h3 class="font-medium text-gray-900 group-hover:text-sage-600 transition-colors">{{ $category->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $category->products_count ?? 0 }} produk</p>
                    </a>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-gray-500">Belum ada kategori tersedia</p>
            </div>
        @endif
        
        <div class="text-center mt-8">
            <a href="{{ route('categories.index') }}" 
               class="inline-flex items-center text-sage-600 hover:text-sage-700 font-medium">
                Lihat Semua Kategori 
                <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Produk Terbaru</h2>
            <p class="text-gray-600">Produk-produk terbaru dari penjual terpercaya</p>
        </div>
        
        @if($featuredProducts && $featuredProducts->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                @foreach($featuredProducts as $product)
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
                            </div>
                            
                            <div class="p-4">
                                <h3 class="font-medium text-gray-900 mb-2 line-clamp-2">{{ $product->name }}</h3>
                                <p class="text-lg font-bold text-sage-600 mb-2">{{ $product->formatted_price }}</p>
                                <div class="flex items-center justify-between text-sm text-gray-500">
                                    <span>{{ $product->store->name ?? 'Toko' }}</span>
                                    <span class="bg-gray-100 px-2 py-1 rounded text-xs">{{ $product->condition }}</span>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-gray-500">Belum ada produk tersedia</p>
            </div>
        @endif
        
        <div class="text-center">
            <a href="{{ route('products.index') }}" 
               class="bg-sage-600 text-white px-8 py-3 rounded-lg hover:bg-sage-700 transition-colors inline-flex items-center">
                Lihat Semua Produk
                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>
</section>

<!-- Popular Stores Section -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Toko Populer</h2>
            <p class="text-gray-600">Toko-toko terpercaya dengan produk berkualitas</p>
        </div>
        
        @if($popularStores && $popularStores->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($popularStores as $store)
                    <a href="{{ route('stores.show', $store) }}" 
                       class="block bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-center space-x-4 mb-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-sage-100 to-sage-200 rounded-full flex items-center justify-center">
                                <span class="text-sage-600 font-bold">{{ substr($store->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $store->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $store->city ?? 'Kota' }}, {{ $store->province ?? 'Provinsi' }}</p>
                            </div>
                        </div>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $store->description ?? 'Deskripsi toko' }}</p>
                        <div class="flex justify-between text-sm text-gray-500">
                            <span>{{ $store->products_count ?? 0 }} produk</span>
                            <span class="text-sage-600">{{ $store->is_active ? 'Aktif' : 'Tidak Aktif' }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-gray-500">Belum ada toko tersedia</p>
            </div>
        @endif
        
        <div class="text-center mt-8">
            <a href="{{ route('stores.index') }}" 
               class="inline-flex items-center text-sage-600 hover:text-sage-700 font-medium">
                Lihat Semua Toko
                <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>
</section>
@endsection