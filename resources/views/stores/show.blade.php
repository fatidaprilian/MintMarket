@extends('layouts.app')

@section('title', $store->name)

@push('styles')
<style>
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
    .store-header-bg {
        background: linear-gradient(135deg, #6b7280 0%, #374151 100%);
        position: relative;
        overflow: hidden;
    }
    .store-header-bg::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(107, 114, 128, 0.9), rgba(55, 65, 81, 0.9));
        z-index: 1;
    }
    .store-header-content {
        position: relative;
        z-index: 2;
    }
    .store-header-content h1 {
        color: #f9fafb; /* Light gray text for visibility */
    }
    .store-header-content .text-white {
        color: #d1d5db; /* Adjust to a slightly darker gray for better contrast */
    }
    .store-header-content .flex .text-white {
        color: #e5e7eb; /* Ensure all text elements in flex containers are visible */
    }
    .rating-overview {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    .stat-card {
        transition: all 0.3s ease;
    }
    .trust-badge {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
                    <a href="{{ route('stores.index') }}" class="ml-1 text-gray-700 hover:text-sage-600 md:ml-2 transition-colors">Toko</a>
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

    <!-- Store Header -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
        <div class="store-header-bg px-8 py-12">
            <div class="store-header-content">
                <div class="flex flex-col lg:flex-row items-start lg:items-center space-y-6 lg:space-y-0 lg:space-x-8">
                    <!-- Store Avatar & Basic Info -->
                    <div class="flex items-center space-x-6">
                        <div class="w-24 h-24 bg-sage-600 backdrop-blur-sm rounded-full flex items-center justify-center border-4 border-sage-600">
                            <span class="text-black font-bold text-3xl">{{ strtoupper(substr($store->name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-black mb-2">{{ $store->name }}</h1>
                            <div class="flex flex-wrap items-center gap-4 text-black/90">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $store->city }}, {{ $store->province }}
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Bergabung {{ $store->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Store Rating & Status -->
                    <div class="flex-1 lg:text-right space-y-4">
                        <!-- Store Rating -->
                        <div class="rating-overview text-black p-4 rounded-lg backdrop-blur-sm bg-white/10">
                            @php
                                // Mock data untuk demo - nanti akan diganti dengan data asli dari database
                                $storeRating = 4.7;
                                $totalReviews = 156;
                                $totalSold = 1234;
                                $responseRate = 98;
                                $avgResponseTime = '2 jam';
                            @endphp
                            <div class="flex items-center justify-center lg:justify-end space-x-2 mb-2">
                                <div class="flex items-center space-x-1 store-rating-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-5 h-5 {{ $i <= floor($storeRating) ? 'text-yellow-300' : 'text-white/30' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    @endfor
                                </div>
                                <span class="text-xl font-bold">{{ number_format($storeRating, 1) }}</span>
                            </div>
                            <p class="text-sm text-black/90">({{ number_format($totalReviews) }} ulasan toko)</p>
                        </div>

                        <!-- Store Status & Trust Badge -->
                        <div class="flex flex-col items-center lg:items-end space-y-2">
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    {{ $store->is_active ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $store->is_active ? 'Toko Aktif' : 'Toko Tidak Aktif' }}
                                </span>
                                @if($storeRating >= 4.5)
                                    <span class="trust-badge inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-500 text-white">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                        Toko Terpercaya
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        @if($store->description)
            <div class="px-8 py-6 bg-gray-50">
                <h3 class="font-semibold text-gray-900 mb-3">Tentang Toko</h3>
                <p class="text-gray-700 leading-relaxed">{{ $store->description }}</p>
            </div>
        @endif
    </div>

    <!-- Store Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Products -->
        <div class="stat-card bg-white rounded-lg shadow-sm p-6 text-center border-l-4 border-sage-500">
            <div class="w-12 h-12 bg-sage-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-sage-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($products->total()) }}</h3>
            <p class="text-gray-600">Total Produk</p>
        </div>
        
        <!-- Total Sold -->
        <div class="stat-card bg-white rounded-lg shadow-sm p-6 text-center border-l-4 border-green-500">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 011.036 1.06 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-1.036 1.06 3.42 3.42 0 01-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 01-1.946-.806 3.42 3.42 0 01-1.036-1.06 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 011.036-1.06z"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($totalSold) }}</h3>
            <p class="text-gray-600">Produk Terjual</p>
        </div>
        
        <!-- Response Rate -->
        <div class="stat-card bg-white rounded-lg shadow-sm p-6 text-center border-l-4 border-blue-500">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $responseRate }}%</h3>
            <p class="text-gray-600">Tingkat Respon</p>
        </div>
        
        <!-- Response Time -->
        <div class="stat-card bg-white rounded-lg shadow-sm p-6 text-center border-l-4 border-yellow-500">
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $avgResponseTime }}</h3>
            <p class="text-gray-600">Rata-rata Respon</p>
        </div>
    </div>

    <!-- Rating Overview -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
        <h3 class="text-xl font-semibold text-gray-900 mb-6">Rating & Ulasan Toko</h3>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Rating Summary -->
            <div class="text-center lg:text-left">
                <div class="flex items-center justify-center lg:justify-start space-x-4 mb-4">
                    <div class="text-5xl font-bold text-gray-900">{{ number_format($storeRating, 1) }}</div>
                    <div>
                        <div class="flex items-center space-x-1 store-rating-stars mb-1">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-6 h-6 {{ $i <= floor($storeRating) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            @endfor
                        </div>
                        <p class="text-gray-600">{{ number_format($totalReviews) }} ulasan toko</p>
                    </div>
                </div>
                <p class="text-gray-700 mb-4">Rating berdasarkan pengalaman berbelanja dengan toko ini</p>
            </div>

            <!-- Rating Breakdown -->
            <div class="space-y-3">
                @php
                    $ratingBreakdown = [
                        5 => 75,
                        4 => 15,
                        3 => 6,
                        2 => 3,
                        1 => 1
                    ];
                @endphp
                @foreach($ratingBreakdown as $stars => $percentage)
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-600 w-8">{{ $stars }} ⭐</span>
                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                            <div class="bg-yellow-400 h-2 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                        </div>
                        <span class="text-sm text-gray-600 w-12">{{ $percentage }}%</span>
                    </div>
                @endforeach
            </div>
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
                    <div class="bg-gray-50 rounded-lg shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden group">
                        <a href="{{ route('products.show', $product) }}">
                            <div class="aspect-square bg-gray-200 relative overflow-hidden">
                                @if($product->main_image)
                                    <img src="{{ asset('storage/' . $product->main_image) }}" 
                                         alt="{{ $product->name }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                @endif
                                
                                <!-- Condition Badge -->
                                <div class="absolute top-2 left-2">
                                    <span class="bg-white/90 backdrop-blur-sm text-gray-700 px-2 py-1 rounded text-xs font-medium">
                                        {{ ucfirst($product->condition) }}
                                    </span>
                                </div>

                                <!-- Quick View Overlay -->
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
                            </div>
                            
                            <div class="p-4">
                                <h3 class="font-medium text-gray-900 mb-2 line-clamp-2 leading-5 group-hover:text-sage-600 transition-colors">{{ $product->name }}</h3>
                                <p class="text-lg font-bold text-sage-600 mb-2">{{ $product->formatted_price }}</p>
                                <div class="flex items-center justify-between text-sm text-gray-500">
                                    <span class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $product->category->name }}</span>
                                    <span class="text-xs font-medium {{ $product->status === 'tersedia' ? 'text-green-600' : 'text-red-600' }}">
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2 2a1 1 0 01-.707.293H9.414a1 1 0 01-.707-.293l-2-2a1 1 0 00-.707-.293H4"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Produk</h3>
                <p class="text-gray-500">Toko ini belum memiliki produk untuk dijual</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Auto refresh status setiap 30 detik (optional)
    setInterval(function() {
        // Refresh store status if needed
    }, 30000);

    // Smooth scroll untuk rating section
    function scrollToRatings() {
        document.querySelector('.rating-overview').scrollIntoView({ 
            behavior: 'smooth' 
        });
    }
</script>
@endpush
@endsection