@extends('layouts.app')

@section('title', $product->name)

@push('styles')
<style>
    /* Custom Sage Colors - Disesuaikan dari palet warna Anda */
    .bg-sage-600 { background-color: #A7C1A8; } /* Hijau yang lebih cerah */
    .bg-sage-700 { background-color: #819A91; } /* Hijau yang lebih gelap */
    .bg-sage-50 { background-color: #F7F9F7; }  /* Hampir putih, latar belakang ringan */
    .text-sage-600 { color: #A7C1A8; }
    .text-sage-700 { color: #819A91; }
    .border-sage-600 { border-color: #A7C1A8; }
    .border-sage-500 { border-color: #819A91; }
    .border-sage-300 { border-color: #D1D8BE; } /* Warna krem kehijauan */
    .hover\:bg-sage-700:hover { background-color: #819A91; }
    .hover\:bg-sage-50:hover { background-color: #F7F9F7; }
    .hover\:text-sage-600:hover { color: #A7C1A8; }
    .hover\:border-sage-300:hover { border-color: #D1D8BE; }
    .focus\:border-sage-500:focus { border-color: #819A91; }
    .focus\:ring-sage-500:focus { --tw-ring-color: #819A91; }
    
    /* Quantity Input Styling */
    .quantity-input {
        -moz-appearance: textfield; /* Untuk Firefox */
        appearance: textfield;
    }
    .quantity-input::-webkit-outer-spin-button,
    .quantity-input::-webkit-inner-spin-button {
        -webkit-appearance: none; /* Untuk Chrome, Safari, Edge */
        margin: 0;
    }
    
    /* Image Gallery */
    .image-gallery-main {
        transition: transform 0.3s ease;
        cursor: zoom-in;
    }
    .image-gallery-main:hover {
        transform: scale(1.02);
    }
    
    /* Sticky Buy Bar untuk mobile */
    .sticky-buy-bar {
        backdrop-filter: blur(10px);
        background-color: rgba(255, 255, 255, 0.95);
        box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    /* Responsive padding untuk konten utama saat sticky bar aktif */
    @media (max-width: 1024px) {
        .main-content {
            padding-bottom: 100px; /* Memberi ruang di bagian bawah untuk sticky bar */
        }
    }
    
    /* Line Clamp untuk judul produk */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    /* Button Hover Effects */
    .btn-primary {
        transition: all 0.2s ease;
    }
    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(167, 193, 168, 0.3); /* Shadow dengan warna sage */
    }
    
    .btn-secondary {
        transition: all 0.2s ease;
    }
    .btn-secondary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(209, 216, 190, 0.3); /* Shadow dengan warna krem kehijauan */
    }

    /* Perbaikan untuk tampilan Breadcrumb agar lebih rapih */
    .breadcrumb-nav ol {
        display: flex; /* Memastikan ol menggunakan flexbox */
        align-items: center;
        padding: 0; /* Pastikan tidak ada padding default pada ol */
        margin: 0; /* Pastikan tidak ada margin default pada ol */
    }

    .breadcrumb-nav li {
        display: flex;
        align-items: center;
    }

    .breadcrumb-nav li:not(:first-child) {
        margin-left: 0.25rem; /* Jarak antar item breadcrumb */
    }

    .breadcrumb-nav li svg {
        margin: 0 0.25rem; /* Memberikan sedikit ruang di sekitar panah SVG */
        vertical-align: middle;
    }
</style>
@endpush

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 main-content">
        <nav class="flex mb-6 breadcrumb-nav" aria-label="Breadcrumb">
            <ol class="inline-flex items-center text-sm">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}" class="text-gray-500 hover:text-sage-600 transition-colors">
                        Beranda
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <a href="{{ route('products.index') }}" class="text-gray-500 hover:text-sage-600 transition-colors">
                        Produk
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <a href="{{ route('categories.show', $product->category) }}" class="text-gray-500 hover:text-sage-600 transition-colors">
                        {{ $product->category->name }}
                    </a>
                </li>
                <li class="breadcrumb-item" aria-current="page">
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-gray-700 truncate max-w-xs">{{ $product->name }}</span>
                </li>
            </ol>
        </nav>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
                <div class="p-6 border-r border-gray-100">
                    <div class="aspect-square bg-gray-100 rounded-xl overflow-hidden mb-4 relative group" onclick="ProductGallery.openModal()">
                        @if($product->image)
                            <img id="mainImage" 
                                 src="{{ asset('storage/' . $product->image) }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-full h-full object-cover image-gallery-main">
                            
                            <div class="absolute top-4 right-4 bg-white/80 backdrop-blur-sm rounded-full p-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                </svg>
                            </div>
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <div class="text-center">
                                    <svg class="w-20 h-20 mx-auto mb-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                    </svg>
                                    <p class="text-sm font-medium">Gambar tidak tersedia</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if($product->images && count($product->images) > 0)
                        <div class="grid grid-cols-5 gap-2">
                            @if($product->image)
                                <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden cursor-pointer border-2 border-sage-500 hover:border-sage-600 transition-colors" 
                                        onclick="ProductGallery.changeMainImage('{{ asset('storage/' . $product->image) }}', this)">
                                    <img src="{{ asset('storage/' . $product->image) }}" 
                                            alt="{{ $product->name }}" 
                                            class="w-full h-full object-cover">
                                </div>
                            @endif
                            
                            @foreach($product->images as $image)
                                <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden cursor-pointer border-2 border-transparent hover:border-sage-300 transition-colors" 
                                        onclick="ProductGallery.changeMainImage('{{ asset('storage/' . $image) }}', this)">
                                    <img src="{{ asset('storage/' . $image) }}" 
                                            alt="{{ $product->name }}" 
                                            class="w-full h-full object-cover">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="p-6 space-y-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 mb-3 leading-tight">{{ $product->name }}</h1>
                        <div class="flex items-center gap-2 mb-4">
                            <span class="px-3 py-1 rounded-full text-sm font-medium {{ $product->condition === 'baru' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ ucfirst($product->condition) }}
                            </span>
                            <span class="px-3 py-1 rounded-full text-sm font-medium {{ $product->status === 'tersedia' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($product->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="pb-4 border-b border-gray-100">
                        <div class="text-3xl font-bold text-sage-700 mb-2">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center space-x-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= 4 ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                @endfor
                                <span class="text-sm text-gray-600 ml-2">4.5 (24 ulasan toko)</span>
                            </div>
                            <span class="text-gray-300">•</span>
                            <span class="text-sm text-gray-600">85 terjual</span>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-4">
                        <h3 class="font-semibold text-gray-900 mb-3">Informasi Toko</h3>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-sage-600 to-sage-700 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-lg">{{ strtoupper(substr($product->store->name, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $product->store->name }}</h4>
                                    <p class="text-sm text-gray-600">{{ $product->store->city ?? 'Jakarta Selatan' }}, DKI Jakarta</p>
                                    <div class="flex items-center space-x-2 mt-1">
                                        <span class="text-xs text-gray-500">Aktif 16 hours ago</span>
                                        <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded">Online</span>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('stores.show', $product->store) }}" 
                               class="bg-white border border-sage-600 text-sage-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-sage-50 transition-colors">
                                Lihat Toko
                            </a>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-4">
                        <h3 class="font-semibold text-gray-900 mb-3">Detail Produk</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Kondisi:</span>
                                    <span class="font-medium">{{ ucfirst($product->condition) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Berat:</span>
                                    <span class="font-medium">{{ $product->weight ?? '924' }} gr</span>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Kategori:</span>
                                    <span class="font-medium">{{ $product->category->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Stok:</span>
                                    <span class="font-medium text-sage-600">{{ $product->stock ?? '18' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @auth
                        @if($product->status === 'tersedia')
                            <form action="{{ route('cart.add') }}" method="POST" id="addToCartForm">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah:</label>
                                    <div class="flex items-center w-32">
                                        <button type="button" 
                                                id="decreaseBtn" {{-- Tombol KURANG --}}
                                                class="w-10 h-10 flex items-center justify-center border border-gray-300 rounded-l-lg hover:bg-gray-50 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                            </svg>
                                        </button>
                                        <input type="number" 
                                                id="quantity" {{-- Input KUANTITAS --}}
                                                name="quantity" 
                                                value="1" 
                                                min="1" 
                                                max="{{ $product->stock ?? 99 }}" 
                                                readonly 
                                                class="w-12 h-10 text-center border-t border-b border-gray-300 focus:outline-none quantity-input">
                                        <button type="button" 
                                                id="increaseBtn" {{-- Tombol TAMBAH --}}
                                                class="w-10 h-10 flex items-center justify-center border border-gray-300 rounded-r-lg hover:bg-gray-50 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <button type="submit" 
                                            class="w-full bg-sage-600 text-white font-semibold py-3 px-6 rounded-lg hover:bg-sage-700 btn-primary flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5-6m0 0L4 5H2m5 8h10m0 0v6a2 2 0 01-2 2H7a2 2 0 01-2-2v-6z"></path>
                                        </svg>
                                        Tambah ke Keranjang
                                    </button>
                                    <button type="button" id="buyNowButton"
                                            class="w-full border-2 border-sage-600 text-sage-600 font-semibold py-3 px-6 rounded-lg hover:bg-sage-50 btn-secondary">
                                        Beli Sekarang
                                    </button>
                                </div>
                            </form>
                        @else
                            {{-- Pesan jika produk tidak tersedia --}}
                            <div class="text-center py-8 bg-red-50 rounded-xl border border-red-200">
                                <svg class="w-12 h-12 text-red-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-red-600 font-medium">Produk Tidak Tersedia</p>
                                <p class="text-red-500 text-sm mt-1">Hubungi penjual untuk informasi lebih lanjut</p>
                            </div>
                        @endif
                    @else
                        {{-- Jika user belum login, tampilkan tombol login/daftar --}}
                        <div class="space-y-3">
                            <a href="{{ route('login') }}" 
                               class="block w-full bg-sage-600 text-white py-4 px-6 rounded-lg font-semibold text-center hover:bg-sage-700 btn-primary">
                                Login untuk Membeli
                            </a>
                            <p class="text-center text-sm text-gray-500">
                                Belum punya akun? 
                                <a href="{{ route('register') }}" class="text-sage-600 hover:text-sage-700 font-medium">Daftar di sini</a>
                            </p>
                        </div>
                    @endauth

                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <div class="flex items-center space-x-6">
                            <button id="wishlistButton" 
                                    class="flex items-center space-x-2 text-gray-500 hover:text-red-500 transition-colors group">
                                <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                                <span class="text-sm font-medium">Wishlist</span>
                            </button>
                            <button id="shareButton" 
                                    class="flex items-center space-x-2 text-gray-500 hover:text-sage-600 transition-colors group">
                                <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.364 3.682a3 3 0 105.639-2.364L17.323 14.682a3 3 0 00-6.677 0L8.684 13.342zM9 12a3 3 0 110-6 3 3 0 010 6z"></path>
                                </svg>
                                <span class="text-sm font-medium">Share</span>
                            </button>
                        </div>
                        <span class="text-sm text-gray-400">ID: #{{ $product->id }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if($product->description)
            <div class="bg-white rounded-xl shadow-sm mt-6 p-6">
                <h3 class="font-bold text-gray-900 mb-4 text-lg">Deskripsi Produk</h3>
                <div class="prose prose-sm max-w-none text-gray-700">
                    <p class="leading-relaxed">{{ $product->description }}</p>
                </div>
            </div>
        @endif

        @if($relatedProducts && $relatedProducts->count() > 0)
            <div class="mt-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Produk Serupa</h2>
                    <a href="{{ route('categories.show', $product->category) }}" 
                       class="text-sage-600 hover:text-sage-700 font-medium text-sm flex items-center gap-1">
                        Lihat Semua
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                    @foreach($relatedProducts as $relatedProduct)
                        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden group">
                            <a href="{{ route('products.show', $relatedProduct) }}">
                                <div class="aspect-square bg-gray-100 relative overflow-hidden">
                                    @if($relatedProduct->image)
                                        <img src="{{ asset('storage/' . $relatedProduct->image) }}" 
                                             alt="{{ $relatedProduct->name }}" 
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    
                                    <div class="absolute top-2 left-2">
                                        <span class="bg-white/90 text-gray-700 px-2 py-1 rounded text-xs font-medium backdrop-blur-sm">
                                            {{ ucfirst($relatedProduct->condition) }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="p-3">
                                    <h3 class="font-medium text-gray-900 text-sm mb-2 line-clamp-2 leading-5 group-hover:text-sage-600 transition-colors">
                                        {{ $relatedProduct->name }}
                                    </h3>
                                    <p class="text-lg font-bold text-sage-600 mb-2">
                                        Rp {{ number_format($relatedProduct->price, 0, ',', '.') }}
                                    </p>
                                    <div class="flex items-center justify-between text-xs text-gray-500">
                                        <span class="truncate">{{ $relatedProduct->store->name }}</span>
                                        <span class="text-green-600 font-medium">{{ ucfirst($relatedProduct->status) }}</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

@auth
    @if($product->status === 'tersedia')
        <div class="lg:hidden fixed bottom-0 left-0 right-0 z-40 sticky-buy-bar">
            <div class="px-4 py-3">
                <form action="{{ route('cart.add') }}" method="POST" id="mobileAddToCartForm" class="flex gap-3">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    {{-- Input quantity ini akan diperbarui oleh JavaScript agar sesuai dengan input utama --}}
                    <input type="hidden" name="quantity" id="mobileQuantity" value="1"> 
                    
                    <button type="button" id="mobileBuyNowButton"
                            class="flex-1 border-2 border-sage-600 text-sage-600 font-semibold py-3 px-4 rounded-lg text-sm">
                        Beli Sekarang
                    </button>
                    <button type="submit" 
                            class="flex-1 bg-sage-600 text-white font-semibold py-3 px-4 rounded-lg text-sm flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5-6m0 0L4 5H2m5 8h10m0 0v6a2 2 0 01-2 2H7a2 2 0 01-2-2v-6z"></path>
                        </svg>
                        Keranjang
                    </button>
                </form>
            </div>
        </div>
    @endif
@endauth

<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden items-center justify-center p-4">
    <div class="relative max-w-4xl max-h-full">
        <button onclick="ProductGallery.closeModal()" 
                class="absolute -top-12 right-0 text-white hover:text-gray-300 text-lg font-semibold">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <img id="modalImage" src="" alt="" class="max-w-full max-h-full object-contain rounded-lg">
    </div>
</div>

@push('scripts')
<script>
// Class untuk mengontrol kuantitas produk
class QuantityController {
    constructor() {
        this.quantityInput = document.getElementById('quantity');
        this.decreaseBtn = document.getElementById('decreaseBtn');
        this.increaseBtn = document.getElementById('increaseBtn');
        this.mobileQuantityInput = document.getElementById('mobileQuantity'); 

        this.init();
    }
    
    init() {
        if (!this.quantityInput) {
            console.error('ERROR: Quantity input (#quantity) not found. QuantityController will not function.');
            return; 
        }
        
        if (this.decreaseBtn) {
            this.decreaseBtn.addEventListener('click', () => {
                this.updateQuantity(-1);
            });
        } else {
            console.warn('WARNING: Decrease button (#decreaseBtn) not found.');
        }

        if (this.increaseBtn) {
            this.increaseBtn.addEventListener('click', () => {
                this.updateQuantity(1);
            });
        } else {
            console.warn('WARNING: Increase button (#increaseBtn) not found.');
        }

        if (this.mobileQuantityInput) {
            this.mobileQuantityInput.value = this.quantityInput.value;
        }
    }
    
    updateQuantity(change) {
        if (!this.quantityInput) {
            console.error('Error: quantityInput is null in updateQuantity. This should not happen after init check.');
            return;
        }

        const currentValue = parseInt(this.quantityInput.value) || 1;
        const minValue = parseInt(this.quantityInput.getAttribute('min')) || 1;
        const maxValue = parseInt(this.quantityInput.getAttribute('max')) || 99; 
        
        let newValue = currentValue + change;
        
        if (newValue < minValue) {
            newValue = minValue;
        } else if (newValue > maxValue) {
            newValue = maxValue;
        }

        this.quantityInput.value = newValue;

        if (this.mobileQuantityInput) {
            this.mobileQuantityInput.value = newValue;
        }
    }
}

// Class untuk mengelola galeri gambar produk
class ProductGallery {
    static changeMainImage(src, element) {
        const mainImage = document.getElementById('mainImage');
        if (mainImage) {
            mainImage.src = src;
        }
        
        document.querySelectorAll('[onclick*="changeMainImage"]').forEach(thumb => {
            thumb.classList.remove('border-sage-500');
            thumb.classList.add('border-transparent');
        });
        
        if (element) {
            element.classList.remove('border-transparent');
            element.classList.add('border-sage-500');
        }
    }

    static openModal() {
        const mainImage = document.getElementById('mainImage');
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        
        if (mainImage && mainImage.src && modal && modalImage) {
            modalImage.src = mainImage.src;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden'; 
        }
    }

    static closeModal() {
        const modal = document.getElementById('imageModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }
    }
}

// Class untuk tindakan terkait produk lainnya
class ProductActions {
    constructor() {
        this.buyNowButton = document.getElementById('buyNowButton');
        this.mobileBuyNowButton = document.getElementById('mobileBuyNowButton');
        this.wishlistButton = document.getElementById('wishlistButton');
        this.shareButton = document.getElementById('shareButton');

        this.init();
    }

    init() {
        if (this.buyNowButton) {
            this.buyNowButton.addEventListener('click', () => this.buyNow());
        }
        if (this.mobileBuyNowButton) {
            this.mobileBuyNowButton.addEventListener('click', () => this.buyNow());
        }
        if (this.wishlistButton) {
            this.wishlistButton.addEventListener('click', (event) => this.toggleWishlist(event));
        }
        if (this.shareButton) {
            this.shareButton.addEventListener('click', () => this.shareProduct());
        }
    }

    buyNow() {
        const quantityInput = document.getElementById('quantity');
        const quantity = quantityInput ? quantityInput.value : 1;
        alert(`Fitur beli sekarang dengan ${quantity} item akan segera tersedia! Ini akan langsung menuju halaman checkout.`);
    }

    toggleWishlist(event) {
        const button = event.currentTarget;
        const svg = button.querySelector('svg');
        const span = button.querySelector('span');
        
        if (svg && span) {
            if (svg.style.fill === 'red') {
                svg.style.fill = 'none';
                span.textContent = 'Wishlist';
                button.classList.remove('text-red-500');
                button.classList.add('text-gray-500');
            } else {
                svg.style.fill = 'red';
                span.textContent = 'Tersimpan';
                button.classList.remove('text-gray-500');
                button.classList.add('text-red-500');
            }
        }
    }

    shareProduct() {
        if (navigator.share) {
            navigator.share({
                title: '{{ $product->name }}',
                text: 'Lihat produk ini di MintMarket!',
                url: window.location.href
            }).catch(error => console.error('Error sharing:', error));
        } else {
            navigator.clipboard.writeText(window.location.href).then(() => {
                alert('Link produk telah disalin ke clipboard!');
            }).catch(() => {
                alert('Gagal menyalin link. Silakan salin manual dari address bar.');
            });
        }
    }
}

// Inisialisasi pada saat DOM sudah siap
document.addEventListener('DOMContentLoaded', function() {
    new QuantityController();
    new ProductActions();
    
    const imageModal = document.getElementById('imageModal');
    if (imageModal) {
        imageModal.addEventListener('click', function(e) {
            if (e.target === this) {
                ProductGallery.closeModal();
            }
        });
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            ProductGallery.closeModal();
        }
    });
});
</script>
@endpush
@endsection