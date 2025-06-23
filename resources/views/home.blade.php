@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<section class="bg-gradient-to-r from-primary-600 to-primary-700 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <h1 class="text-4xl md:text-5xl font-bold mb-6">
                    Jual Beli Online <br>
                    <span class="text-primary-200">Mudah & Terpercaya</span>
                </h1>
                <p class="text-xl text-primary-100 mb-8">
                    Temukan produk favorit Anda dengan mudah di MintMarket.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('products.index') }}" 
                       class="bg-white text-primary-600 px-8 py-3 rounded-lg font-semibold hover:bg-primary-50 transition-colors text-center">
                        Mulai Belanja
                    </a>
                </div>
            </div>
            <div class="hidden lg:block">
                <div class="w-full h-80 bg-white/20 rounded-2xl flex items-center justify-center overflow-hidden">
                    {{-- Replaced the SVG placeholder with a suitable online image --}}
                    <img src="https://image.freepik.com/free-vector/online-shopping-banner-with-realistic-smartphone_107791-2309.jpg" 
                         alt="Online Shopping Hero Image" 
                         class="w-full h-full object-cover">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Kategori Pilihan</h2>
            <p class="text-gray-600">Temukan produk sesuai kebutuhan Anda</p>
        </div>
        
        @if($categories && $categories->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-6">
                @foreach($categories as $category)
                    <a href="{{ route('products.index', ['category' => $category->slug]) }}" 
                       class="group p-4 rounded-lg hover:bg-gray-50 transition-colors flex flex-col items-center justify-end h-full min-h-[170px]">
                        <div class="w-16 h-16 bg-gradient-to-br from-primary-100 to-primary-200 rounded-full flex items-center justify-center mb-3 group-hover:from-primary-200 group-hover:to-primary-300 transition-colors">
                            <svg class="w-8 h-8 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path>
                            </svg>
                        </div>
                        <div class="min-h-[44px] flex items-center justify-center text-center">
                            <h3 class="font-medium text-gray-900 group-hover:text-primary-600 transition-colors leading-tight">
                                {{ $category->name }}
                            </h3>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">{{ $category->products_count ?? 0 }} produk</p>
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
               class="inline-flex items-center text-primary-600 hover:text-primary-700 font-medium">
                Lihat Semua Kategori 
                <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>
</section>

@if($flashSaleProducts && $flashSaleProducts->count() > 0)
<section class="py-16 bg-gradient-to-r from-orange-50 to-red-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center space-x-4">
                <h2 class="text-2xl font-bold text-orange-600 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path>
                    </svg>
                    Flash Sale
                </h2>
                
                @php
                    $firstProduct = $flashSaleProducts->first();
                @endphp
                
                @if($firstProduct && $firstProduct->flash_sale_end_date)
                <div class="flex items-center space-x-2" data-countdown-target="{{ $firstProduct->flash_sale_end_date->toIso8601String() }}">
                    <span class="text-gray-700 font-medium">Berakhir dalam:</span>
                    <div class="bg-gray-900 text-white rounded px-2 py-1 text-lg font-mono min-w-[2rem] text-center" id="fs_hours">00</div>
                    <span class="text-gray-900 font-bold">:</span>
                    <div class="bg-gray-900 text-white rounded px-2 py-1 text-lg font-mono min-w-[2rem] text-center" id="fs_minutes">00</div>
                    <span class="text-gray-900 font-bold">:</span>
                    <div class="bg-gray-900 text-white rounded px-2 py-1 text-lg font-mono min-w-[2rem] text-center" id="fs_seconds">00</div>
                </div>
                @endif
            </div>
            <a href="{{ route('products.index') }}?flash_sale=1" class="text-primary-600 hover:text-primary-700 font-medium flex items-center">
                Lihat Semua 
                <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
        
        <div class="relative">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($flashSaleProducts as $product)
                    <div class="bg-white rounded-lg overflow-hidden border border-orange-200 hover:shadow-lg transition-all duration-300 group">
                        <a href="{{ route('products.show', $product) }}" class="block">
                            <div class="aspect-square bg-gray-200 relative overflow-hidden">
                                @if($product->main_image)
                                    <img src="{{ $product->main_image }}" 
                                         alt="{{ $product->name }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                @endif
                                
                                @if($product->discount_percentage > 0)
                                    <div class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-md">
                                        -{{ $product->discount_percentage }}%
                                    </div>
                                @endif
                                
                                <div class="absolute top-2 left-2 bg-orange-500 text-white text-xs font-bold px-2 py-1 rounded-md animate-pulse">
                                    FLASH SALE
                                </div>
                            </div>
                            
                            <div class="p-3">
                                <h3 class="text-sm text-gray-800 line-clamp-2 mb-2 h-10 font-medium">{{ $product->name }}</h3>
                                <div class="space-y-1">
                                    @if($product->strikethrough_price)
                                        <span class="text-xs text-gray-400 line-through block">{{ $product->formatted_strikethrough_price }}</span>
                                    @endif
                                    <span class="text-base font-bold text-orange-600">{{ $product->formatted_current_price }}</span>
                                </div>
                                
                                <div class="mt-2 text-xs text-gray-500">
                                    <span class="truncate block">{{ $product->store->name }}</span>
                                </div>
                                
                                <div class="mt-2 h-2 bg-orange-100 rounded-full relative overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-orange-400 to-red-500 rounded-full animate-pulse" style="width: {{ rand(30, 80) }}%;"></div>
                                </div>
                                <span class="text-xs text-orange-600 font-medium mt-1 block">Stok Terbatas!</span>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Rekomendasi Untukmu</h2>
            <p class="text-gray-600">Produk pilihan yang mungkin Anda sukai</p>
        </div>
        
        @if($featuredProducts && $featuredProducts->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($featuredProducts as $product)
                    <div class="bg-white rounded-lg shadow-sm hover:shadow-lg transition-shadow overflow-hidden border border-gray-100 group">
                        <a href="{{ route('products.show', $product) }}">
                            <div class="aspect-square bg-gray-200 relative overflow-hidden">
                                @if($product->main_image)
                                    <img src="{{ $product->main_image }}" 
                                         alt="{{ $product->name }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                @endif
                                
                                @if($product->discount_percentage > 0)
                                    <div class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-md">
                                        -{{ $product->discount_percentage }}%
                                    </div>
                                @endif
                            </div>
                            
                            <div class="p-4">
                                <h3 class="font-medium text-gray-900 mb-2 line-clamp-2 h-12 group-hover:text-primary-600 transition-colors">{{ $product->name }}</h3>
                                <div class="mb-2 h-12 flex flex-col justify-center">
                                    @if($product->strikethrough_price)
                                        <span class="text-sm text-gray-400 line-through">{{ $product->formatted_strikethrough_price }}</span>
                                    @endif
                                    <span class="text-lg font-bold text-primary-600">{{ $product->formatted_current_price }}</span>
                                </div>
                                <div class="text-sm text-gray-500 space-y-1 mt-2">
                                    <p class="truncate flex items-center">
                                        <svg class="w-4 h-4 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        {{ $product->store->name ?? 'N/A' }}
                                    </p>
                                    <div class="flex items-center divide-x divide-gray-300">
                                        <span class="pr-2 flex items-center">
                                            <svg class="w-4 h-4 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            {{ $product->store->city ?? 'Indonesia' }}
                                        </span>
                                        <span class="pl-2">{{ ucfirst($product->condition) }}</span>
                                    </div>
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
        
        <div class="text-center mt-12">
            <a href="{{ route('products.index') }}" class="bg-primary-600 text-white px-10 py-3 rounded-lg hover:bg-primary-700 transition-colors inline-flex items-center font-semibold">
                Lihat Lebih Banyak
                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Custom primary colors fallback */
:root {
    --color-primary-50: #eff6ff;
    --color-primary-100: #dbeafe;
    --color-primary-200: #bfdbfe;
    --color-primary-300: #93c5fd;
    --color-primary-400: #60a5fa;
    --color-primary-500: #3b82f6;
    --color-primary-600: #2563eb;
    --color-primary-700: #1d4ed8;
}
</style>
@endpush

@push('scripts')
<script>
function startFlashSaleCountdown() {
    const countdownContainer = document.querySelector('[data-countdown-target]');
    if (!countdownContainer) {
        console.log('No countdown container found');
        return;
    }

    const hoursEl = document.getElementById('fs_hours');
    const minutesEl = document.getElementById('fs_minutes');
    const secondsEl = document.getElementById('fs_seconds');

    if (!hoursEl || !minutesEl || !secondsEl) {
        console.log('Countdown elements not found');
        return;
    }

    const countdownDate = new Date(countdownContainer.dataset.countdownTarget).getTime();
    console.log('Countdown target:', countdownContainer.dataset.countdownTarget);

    const timer = setInterval(function() {
        const now = new Date().getTime();
        const distance = countdownDate - now;
        
        if (distance < 0) {
            clearInterval(timer);
            hoursEl.innerHTML = '00';
            minutesEl.innerHTML = '00';
            secondsEl.innerHTML = '00';
            
            // Optional: Refresh halaman atau sembunyikan section
            setTimeout(() => {
                window.location.reload();
            }, 1000);
            return;
        }
        
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        hoursEl.innerHTML = String(hours).padStart(2, '0');
        minutesEl.innerHTML = String(minutes).padStart(2, '0');
        secondsEl.innerHTML = String(seconds).padStart(2, '0');
    }, 1000);
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, starting countdown...');
    startFlashSaleCountdown();
});
</script>
@endpush