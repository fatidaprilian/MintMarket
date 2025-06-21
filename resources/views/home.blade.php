@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-r from-sage-600 to-sage-700 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <h1 class="text-4xl md:text-5xl font-bold mb-6">
                    Jual Beli Online <br>
                    <span class="text-sage-200">Mudah & Terpercaya</span>
                </h1>
                <p class="text-xl text-sage-100 mb-8">
                    Temukan produk favorit Anda dengan mudah di MintMarket.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('products.index') }}" 
                       class="bg-white text-sage-600 px-8 py-3 rounded-lg font-semibold hover:bg-sage-50 transition-colors text-center">
                        Mulai Belanja
                    </a>
                </div>
            </div>
            <div class="hidden lg:block">
                <div class="w-full h-80 bg-white/20 rounded-2xl flex items-center justify-center">
                     <svg class="w-24 h-24 text-white/50" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Kategori Pilihan</h2>
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

<!-- Flash Sale Section -->
@if($flashSaleProducts && $flashSaleProducts->count() > 0)
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center space-x-4">
                <h2 class="text-2xl font-bold text-orange-600">Flash Sale</h2>
                <div class="flex items-center space-x-2" data-countdown-target="{{ $flashSaleProducts->first()->flash_sale_end_date->toIso8601String() }}">
                    <div class="bg-gray-900 text-white rounded px-2 py-1 text-lg font-mono" id="fs_hours"></div>
                    <span class="text-gray-900 font-bold">:</span>
                    <div class="bg-gray-900 text-white rounded px-2 py-1 text-lg font-mono" id="fs_minutes"></div>
                    <span class="text-gray-900 font-bold">:</span>
                    <div class="bg-gray-900 text-white rounded px-2 py-1 text-lg font-mono" id="fs_seconds"></div>
                </div>
            </div>
            <a href="{{ route('products.index') }}?flash_sale=1" class="text-sage-600 hover:text-sage-700 font-medium flex items-center">
                Lihat Semua <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>
        
        <div class="relative">
            <div class="flex space-x-3 overflow-x-auto pb-4 scrollbar-hide">
                @foreach($flashSaleProducts as $product)
                    <div class="flex-shrink-0 w-40">
                        <a href="{{ route('products.show', $product) }}" class="block bg-white rounded-lg overflow-hidden border border-gray-200 hover:shadow-md transition-shadow">
                            <div class="aspect-square bg-gray-200 relative">
                                @if($product->main_image)
                                    <img src="{{ asset('storage/' . $product->main_image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400"><svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"></path></svg></div>
                                @endif
                                @if($product->flash_sale_discount_percentage > 0)
                                    <div class="absolute top-0 right-0 bg-orange-500 text-white text-xs font-bold px-2 py-1">
                                        -{{ $product->flash_sale_discount_percentage }}%
                                    </div>
                                @endif
                            </div>
                            <div class="p-2">
                                <h3 class="text-sm text-gray-800 line-clamp-2 mb-2 h-10">{{ $product->name }}</h3>
                                <div class="space-y-1">
                                    <span class="text-xs text-gray-400 line-through block">{{ $product->formatted_price }}</span>
                                    <span class="text-base font-bold text-orange-600">{{ $product->formatted_current_price }}</span>
                                </div>
                                <div class="mt-2 h-4 bg-orange-100 rounded-full relative">
                                    <div class="h-full bg-orange-500 rounded-full" style="width: {{ rand(30, 80) }}%;"></div>
                                    <span class="absolute inset-0 text-white text-xs font-semibold flex items-center justify-center">Stok Terbatas</span>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

<!-- Rekomendasi Untukmu -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Rekomendasi Untukmu</h2>
        </div>
        
        @if($featuredProducts && $featuredProducts->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($featuredProducts as $product)
                    <div class="bg-white rounded-lg shadow-sm hover:shadow-lg transition-shadow overflow-hidden border border-gray-100">
                        <a href="{{ route('products.show', $product) }}">
                            <div class="aspect-square bg-gray-200 relative">
                                @if($product->main_image)
                                    <img src="{{ asset('storage/' . $product->main_image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400"><svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"></path></svg></div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="font-medium text-gray-900 mb-2 line-clamp-2 h-10">{{ $product->name }}</h3>
                                <div class="mb-2 h-12 flex flex-col justify-center">
                                    @if($product->strikethrough_price)
                                        <span class="text-sm text-gray-400 line-through">{{ $product->formatted_strikethrough_price }}</span>
                                    @endif
                                    <span class="text-lg font-bold text-sage-600">{{ $product->formatted_current_price }}</span>
                                </div>
                                <div class="text-sm text-gray-500 space-y-1 mt-2">
                                     <p class="truncate flex items-center">
                                         <svg class="w-4 h-4 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                         {{ $product->store->name ?? 'N/A' }}
                                    </p>
                                     <div class="flex items-center divide-x divide-gray-300">
                                         <span class="pr-2 flex items-center">
                                            <svg class="w-4 h-4 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
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
        @endif
        
        <div class="text-center mt-12">
            <a href="{{ route('products.index') }}" class="bg-sage-600 text-white px-10 py-3 rounded-lg hover:bg-sage-700 transition-colors inline-flex items-center font-semibold">
                Lihat Lebih Banyak
            </a>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<style>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
<script>
function startFlashSaleCountdown() {
    const countdownContainer = document.querySelector('[data-countdown-target]');
    if (!countdownContainer) return;

    const hoursEl = document.getElementById('fs_hours');
    const minutesEl = document.getElementById('fs_minutes');
    const secondsEl = document.getElementById('fs_seconds');

    if (!hoursEl || !minutesEl || !secondsEl) return;

    const countdownDate = new Date(countdownContainer.dataset.countdownTarget).getTime();

    const timer = setInterval(function() {
        const now = new Date().getTime();
        const distance = countdownDate - now;
        
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        hoursEl.innerHTML = String(hours).padStart(2, '0');
        minutesEl.innerHTML = String(minutes).padStart(2, '0');
        secondsEl.innerHTML = String(seconds).padStart(2, '0');
        
        if (distance < 0) {
            clearInterval(timer);
            hoursEl.innerHTML = '00';
            minutesEl.innerHTML = '00';
            secondsEl.innerHTML = '00';
            // Optional: Sembunyikan atau ubah section flash sale
            countdownContainer.closest('section').style.display = 'none';
        }
    }, 1000);
}

document.addEventListener('DOMContentLoaded', function() {
    startFlashSaleCountdown();
});
</script>
@endpush
