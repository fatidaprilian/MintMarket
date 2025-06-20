@extends('layouts.app')

@section('title', $product->name)

@push('styles')
<style>
    .image-gallery-main {
        transition: transform 0.3s ease;
    }
    .image-gallery-main:hover {
        transform: scale(1.05);
    }
    .quantity-btn:hover {
        background-color: #f3f4f6;
    }
    .sticky-buy-bar {
        backdrop-filter: blur(10px);
        background-color: rgba(255, 255, 255, 0.95);
    }
    @media (max-width: 1024px) {
        .main-content {
            padding-bottom: 100px;
        }
    }
    .store-rating-stars {
        display: flex;
        align-items: center;
        gap: 2px;
    }
    .star-rating {
        color: #fbbf24;
        transition: transform 0.2s ease;
    }
    .star-rating:hover {
        transform: scale(1.1);
    }
    
    /* Custom Sage Colors */
    .bg-sage-600 { background-color: #819A91; }
    .bg-sage-700 { background-color: #6B8A6B; }
    .bg-sage-50 { background-color: #F7F9F7; }
    .text-sage-600 { color: #819A91; }
    .border-sage-600 { border-color: #819A91; }
    .border-sage-500 { border-color: #A7C1A8; }
    .border-sage-300 { border-color: #D1D8BE; }
    .hover\:bg-sage-700:hover { background-color: #6B8A6B; }
    .hover\:bg-sage-50:hover { background-color: #F7F9F7; }
    .hover\:text-sage-600:hover { color: #819A91; }
    .hover\:text-sage-700:hover { color: #6B8A6B; }
    .hover\:border-sage-300:hover { border-color: #D1D8BE; }
    .focus\:border-sage-500:focus { border-color: #A7C1A8; }
    .focus\:ring-sage-500:focus { --tw-ring-color: #A7C1A8; }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 main-content">
    <!-- Breadcrumb -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('home') }}" class="text-gray-700 hover:text-sage-600 transition-colors">Beranda</a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <a href="{{ route('products.index') }}" class="ml-1 text-gray-700 hover:text-sage-600 md:ml-2 transition-colors">Produk</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <a href="{{ route('categories.show', $product->category) }}" class="ml-1 text-gray-700 hover:text-sage-600 md:ml-2 transition-colors">{{ $product->category->name }}</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-gray-500 md:ml-2 truncate max-w-xs">{{ $product->name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Product Detail -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 mb-12">
        <!-- Product Images -->
        <div class="space-y-4">
            <!-- Main Image -->
            <div class="aspect-square bg-gray-200 rounded-lg overflow-hidden relative group cursor-pointer" onclick="openImageModal()">
                @if($product->main_image)
                    <img id="mainImage" src="{{ asset('storage/' . $product->main_image) }}" 
                         alt="{{ $product->name }}" 
                         class="w-full h-full object-cover image-gallery-main">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                        <div class="text-center">
                            <svg class="w-24 h-24 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                            </svg>
                            <p class="text-sm">Gambar tidak tersedia</p>
                        </div>
                    </div>
                @endif
                
                <!-- Zoom indicator -->
                @if($product->main_image)
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all duration-300 flex items-center justify-center">
                        <div class="bg-white rounded-full p-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                            </svg>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Thumbnail Images -->
            @if($product->images && count($product->images) > 0)
                <div class="grid grid-cols-4 gap-2">
                    <!-- Main image thumbnail -->
                    @if($product->main_image)
                        <div class="aspect-square bg-gray-200 rounded-lg overflow-hidden cursor-pointer border-2 border-sage-500" onclick="changeMainImage('{{ asset('storage/' . $product->main_image) }}', this)">
                            <img src="{{ asset('storage/' . $product->main_image) }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                        </div>
                    @endif
                    
                    <!-- Additional images -->
                    @foreach($product->images as $image)
                        <div class="aspect-square bg-gray-200 rounded-lg overflow-hidden cursor-pointer border-2 border-transparent hover:border-sage-300 transition-colors" onclick="changeMainImage('{{ asset('storage/' . $image) }}', this)">
                            <img src="{{ asset('storage/' . $image) }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Product Info -->
        <div class="space-y-6">
            <!-- Product Title & Price -->
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-4 leading-tight">{{ $product->name }}</h1>
                <div class="flex items-center space-x-4 mb-4">
                    <span class="text-4xl font-bold text-sage-600">{{ $product->formatted_price }}</span>
                    <div class="flex items-center space-x-2">
                        <span class="bg-{{ $product->condition === 'baru' ? 'green' : 'blue' }}-100 text-{{ $product->condition === 'baru' ? 'green' : 'blue' }}-800 px-3 py-1 rounded-full text-sm font-medium">
                            {{ ucfirst($product->condition) }}
                        </span>
                        <span class="px-3 py-1 rounded-full text-sm font-medium {{ $product->status === 'tersedia' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($product->status) }}
                        </span>
                    </div>
                </div>
                
                <!-- Store Rating (SINGLE LOCATION) -->
                <div class="flex items-center space-x-4 mb-6">
                    <div class="flex items-center space-x-1 store-rating-stars">
                        @php
                            // Mock data untuk demo - nanti akan diganti dengan rating toko yang sesungguhnya
                            $storeRating = 4.5;
                            $totalReviews = 24;
                            $totalSold = rand(50, 200);
                        @endphp
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4 star-rating {{ $i <= floor($storeRating) ? 'text-yellow-400' : ($i <= $storeRating ? 'text-yellow-300' : 'text-gray-300') }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        @endfor
                        <span class="text-sm text-gray-600 ml-2 font-medium">{{ number_format($storeRating, 1) }} ({{ $totalReviews }} ulasan toko)</span>
                    </div>
                    <span class="text-gray-300">|</span>
                    <span class="text-sm text-gray-600 font-medium">{{ $totalSold }} terjual</span>
                </div>
            </div>

            <!-- Store Info (Simplified - No Redundant Rating) -->
            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                <h3 class="font-semibold text-gray-900 mb-4">Informasi Toko</h3>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-sage-600 to-sage-700 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold text-lg">{{ strtoupper(substr($product->store->name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900 text-lg">{{ $product->store->name }}</h4>
                            <p class="text-sm text-gray-600">{{ $product->store->city }}, {{ $product->store->province }}</p>
                            <div class="flex items-center space-x-2 mt-1">
                                <span class="text-xs text-gray-500">Aktif {{ $product->store->created_at->diffForHumans() }}</span>
                                @if($product->store->is_active)
                                    <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded">Online</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('stores.show', $product->store) }}" 
                       class="bg-white border border-sage-600 text-sage-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-sage-50 transition-colors">
                        Lihat Toko
                    </a>
                </div>
            </div>

            <!-- Product Specifications -->
            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                <h3 class="font-semibold text-gray-900 mb-4">Detail Produk</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Kondisi:</span>
                        <span class="font-medium">{{ ucfirst($product->condition) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Kategori:</span>
                        <span class="font-medium">{{ $product->category->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Berat:</span>
                        <span class="font-medium">{{ $product->weight ?? '500' }} gr</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Stok:</span>
                        <span class="font-medium text-green-600">{{ $product->stock ?? 'Tersedia' }}</span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            @auth
                @if($product->status === 'tersedia')
                    {{-- FORM AKSI UNTUK DESKTOP --}}
                    <form action="{{ route('cart.add') }}" method="POST" class="mt-6">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        
                        <div class="flex items-center gap-4 mb-4">
                            <label for="quantity_desktop" class="font-semibold dark:text-gray-200">Jumlah:</label>
                            <div class="flex items-center border border-gray-300 rounded-md">
                                <button type="button" onclick="updateQuantity('quantity_desktop', -1)" class="quantity-btn p-2 rounded-l-md dark:text-gray-200">-</button>
                                <input type="number" id="quantity_desktop" name="quantity" value="1" min="1" max="{{ $product->stock ?? 99 }}" 
                                       class="w-16 text-center border-x-0 dark:bg-gray-700 dark:text-gray-200 focus:ring-0">
                                <button type="button" onclick="updateQuantity('quantity_desktop', 1)" class="quantity-btn p-2 rounded-r-md dark:text-gray-200">+</button>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3">
                            {{-- KONSISTEN: Sage untuk Tambah ke Keranjang --}}
                            <button type="submit" class="w-full bg-sage-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-sage-700 transition duration-300 flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" /></svg>
                                <span>Tambah ke Keranjang</span>
                            </button>
                            {{-- KONSISTEN: Border Sage untuk Beli Sekarang --}}
                            <button type="button" onclick="buyNow()" class="w-full border-2 border-sage-600 text-sage-600 py-3 px-4 rounded-lg font-bold hover:bg-sage-50 transition duration-300">
                                Beli Sekarang
                            </button>
                        </div>
                    </form>
                @else
                    <div class="text-center py-6 bg-red-50 rounded-lg border border-red-200">
                        <svg class="w-12 h-12 text-red-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-red-600 font-medium">Produk Tidak Tersedia</p>
                        <p class="text-red-500 text-sm mt-1">Hubungi penjual untuk informasi lebih lanjut</p>
                    </div>
                @endif
            @else
                <div class="space-y-3">
                    <a href="{{ route('login') }}" 
                       class="block w-full bg-sage-600 text-white py-4 px-6 rounded-lg font-medium text-center hover:bg-sage-700 transition-all duration-300 transform hover:scale-105">
                        Login untuk Membeli
                    </a>
                    <p class="text-center text-sm text-gray-500">
                        Belum punya akun? <a href="{{ route('register') }}" class="text-sage-600 hover:text-sage-700 font-medium">Daftar di sini</a>
                    </p>
                </div>
            @endauth

            <!-- Social Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <div class="flex items-center space-x-6">
                    <button onclick="toggleWishlist()" class="flex items-center space-x-2 text-gray-500 hover:text-red-500 transition-colors group">
                        <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                        <span class="text-sm font-medium">Wishlist</span>
                    </button>
                    <button onclick="shareProduct()" class="flex items-center space-x-2 text-gray-500 hover:text-sage-600 transition-colors group">
                        <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.364 3.682a3 3 0 105.639-2.364L17.323 14.682a3 3 0 00-6.677 0L8.684 13.342zM9 12a3 3 0 110-6 3 3 0 010 6z"></path>
                        </svg>
                        <span class="text-sm font-medium">Share</span>
                    </button>
                </div>
                <span class="text-sm text-gray-400">ID: #{{ $product->id }}</span>
            </div>

            <!-- Product Description -->
            @if($product->description)
                <div class="pt-6 border-t border-gray-200">
                    <h3 class="font-semibold text-gray-900 mb-3">Deskripsi Produk</h3>
                    <div class="prose prose-sm max-w-none text-gray-700">
                        <p class="leading-relaxed">{{ $product->description }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts && $relatedProducts->count() > 0)
        <div class="mt-16">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Produk Serupa</h2>
                <a href="{{ route('categories.show', $product->category) }}" class="text-sage-600 hover:text-sage-700 font-medium text-sm">
                    Lihat Semua →
                </a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $relatedProduct)
                    <div class="bg-white rounded-lg shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden group">
                        <a href="{{ route('products.show', $relatedProduct) }}">
                            <div class="aspect-square bg-gray-200 relative overflow-hidden">
                                @if($relatedProduct->main_image)
                                    <img src="{{ asset('storage/' . $relatedProduct->main_image) }}" 
                                         alt="{{ $relatedProduct->name }}" 
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                @endif
                                
                                <!-- Overlay with quick actions -->
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300 flex items-center justify-center">
                                    <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        <div class="bg-white rounded-full p-2">
                                            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Condition Badge -->
                                <div class="absolute top-3 left-3">
                                    <span class="bg-white/90 text-gray-700 px-2 py-1 rounded text-xs font-medium backdrop-blur-sm">
                                        {{ ucfirst($relatedProduct->condition) }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="p-4">
                                <h3 class="font-medium text-gray-900 mb-2 line-clamp-2 leading-5 group-hover:text-sage-600 transition-colors">
                                    {{ $relatedProduct->name }}
                                </h3>
                                <p class="text-lg font-bold text-sage-600 mb-3">{{ $relatedProduct->formatted_price }}</p>
                                
                                <!-- Store Rating in Product Card (Optional - bisa dihapus jika dirasa terlalu banyak) -->
                                <div class="flex items-center space-x-2 mb-2">
                                    <div class="flex items-center space-x-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-3 h-3 {{ $i <= 4 ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        @endfor
                                        <span class="text-xs text-gray-600 ml-1">4.0</span>
                                    </div>
                                    <span class="text-xs text-gray-400">•</span>
                                    <span class="text-xs {{ $relatedProduct->status === 'tersedia' ? 'text-green-600' : 'text-red-600' }} font-medium">
                                        {{ ucfirst($relatedProduct->status) }}
                                    </span>
                                </div>
                                
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">{{ $relatedProduct->category->name }}</span>
                                </div>
                                <div class="mt-2 text-xs text-gray-500">
                                    {{ $relatedProduct->store->name }}
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

{{-- STICKY BAR UNTUK MOBILE --}}
@auth
    @if($product->status === 'tersedia')
        <div class="lg:hidden fixed bottom-0 left-0 right-0 z-40 p-2 border-t border-gray-200 dark:border-gray-700 sticky-buy-bar">
            <form action="{{ route('cart.add') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                {{-- Di mobile, kita asumsikan kuantitas selalu 1 untuk simple --}}
                <input type="hidden" name="quantity" value="1">
                
                <div class="flex justify-between items-center gap-2">
                    <button type="button" onclick="buyNow()" class="w-1/2 border-2 border-sage-600 text-sage-600 font-bold py-2 px-3 rounded-md text-sm hover:bg-sage-50">Beli Sekarang</button>
                    <button type="submit" class="w-1/2 bg-sage-600 text-white font-bold py-2 px-3 rounded-md text-sm flex items-center justify-center gap-1 hover:bg-sage-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" /></svg>
                        <span>+ Keranjang</span>
                    </button>
                </div>
            </form>
        </div>
    @endif
@endauth

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden items-center justify-center p-4">
    <div class="relative max-w-4xl max-h-full">
        <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 z-10">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <img id="modalImage" src="" alt="" class="max-w-full max-h-full object-contain rounded-lg">
    </div>
</div>

@push('scripts')
<script>
    // Tambahkan fungsi updateQuantity
    function updateQuantity(inputId, change) {
        const input = document.getElementById(inputId);
        const currentValue = parseInt(input.value);
        const min = parseInt(input.min);
        const max = parseInt(input.max);
        
        const newValue = currentValue + change;
        
        if (newValue >= min && newValue <= max) {
            input.value = newValue;
        }
    }

    // Image gallery functions
    function changeMainImage(src, element) {
        document.getElementById('mainImage').src = src;
        
        // Update active thumbnail
        document.querySelectorAll('[onclick*="changeMainImage"]').forEach(thumb => {
            thumb.classList.remove('border-sage-500');
            thumb.classList.add('border-transparent');
        });
        element.classList.remove('border-transparent');
        element.classList.add('border-sage-500');
    }

    function openImageModal() {
        const mainImage = document.getElementById('mainImage');
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        
        if (mainImage.src) {
            modalImage.src = mainImage.src;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeImageModal() {
        const modal = document.getElementById('imageModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    // Buy now function
    function buyNow() {
        // You can implement buy now logic here
        alert('Fitur beli sekarang akan segera tersedia!');
    }

    // Wishlist toggle
    function toggleWishlist() {
        // Implement wishlist functionality
        const button = event.currentTarget;
        const svg = button.querySelector('svg');
        
        if (svg.style.fill === 'red') {
            svg.style.fill = 'none';
            button.querySelector('span').textContent = 'Wishlist';
        } else {
            svg.style.fill = 'red';
            button.querySelector('span').textContent = 'Tersimpan';
        }
    }

    // Share product
    function shareProduct() {
        if (navigator.share) {
            navigator.share({
                title: '{{ $product->name }}',
                text: 'Lihat produk ini di MintMarket',
                url: window.location.href
            });
        } else {
            // Fallback copy to clipboard
            navigator.clipboard.writeText(window.location.href).then(() => {
                alert('Link produk telah disalin!');
            });
        }
    }

    // Close modal when clicking outside
    document.getElementById('imageModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeImageModal();
        }
    });

    // Keyboard navigation for modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeImageModal();
        }
    });
</script>
@endpush
@endsection