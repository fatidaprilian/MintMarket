@extends('layouts.app')

@section('title', $store->name)

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
                    <a href="{{ route('stores.index') }}" class="ml-1 text-gray-700 hover:text-sage-600 md:ml-2">Toko</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-gray-500 md:ml-2">{{ $store->name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Store Header - CLEAN VERSION -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-8">
        <div class="bg-gradient-to-r from-sage-600 to-sage-700 p-8">
            <div class="flex items-center space-x-6">
                <!-- Store Avatar - SINGLE ELEMENT -->
                <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
                    @php
                        $storeName = trim($store->name ?? '');
                        $initial = $storeName ? strtoupper(substr($storeName, 0, 1)) : 'T';
                    @endphp
                    <span class="text-white font-bold text-2xl">{{ $initial }}</span>
                </div>
                
                <!-- Store Info -->
                <div class="flex-1 min-w-0">
                    <h1 class="text-3xl font-bold text-white mb-3">{{ $storeName }}</h1>
                    <div class="flex flex-wrap items-center gap-4 text-sage-100">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            {{ $store->city }}, {{ $store->province }}
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            {{ $products->total() }} produk
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Bergabung {{ $store->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
                
                <!-- Store Status -->
                <div class="flex-shrink-0">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        {{ $store->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $store->is_active ? 'Toko Aktif' : 'Toko Tidak Aktif' }}
                    </span>
                </div>
            </div>
        </div>
        
        @if($store->description)
            <div class="px-8 py-6">
                <h3 class="font-semibold text-gray-900 mb-3">Tentang Toko</h3>
                <p class="text-gray-700 leading-relaxed">{{ $store->description }}</p>
            </div>
        @endif
    </div>

    <!-- Store Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm p-6 text-center">
            <div class="w-12 h-12 bg-sage-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-sage-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $products->total() }}</h3>
            <p class="text-gray-600">Total Produk</p>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-6 text-center">
            <div class="w-12 h-12 bg-sage-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-sage-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $store->is_active ? 'Aktif' : 'Tidak Aktif' }}</h3>
            <p class="text-gray-600">Status Toko</p>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-6 text-center">
            <div class="w-12 h-12 bg-sage-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-sage-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $store->created_at->format('Y') }}</h3>
            <p class="text-gray-600">Tahun Bergabung</p>
        </div>
    </div>

    <!-- Products Section -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <h2 class="text-xl font-semibold text-gray-900">Produk dari {{ $store->name }}</h2>
            
            <form action="{{ route('stores.show', $store) }}" method="GET" class="flex items-center gap-2">
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

        @if($products->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-6">
                @foreach($products as $product)
                    <div class="bg-gray-50 rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden">
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
                                    <span class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $product->category->name }}</span>
                                    <span class="text-xs {{ $product->status === 'tersedia' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ ucfirst($product->status) }}
                                    </span>
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
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Produk</h3>
                <p class="text-gray-500">Toko ini belum memiliki produk</p>
            </div>
        @endif
    </div>
</div>
@endsection