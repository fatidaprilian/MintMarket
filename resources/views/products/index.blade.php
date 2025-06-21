@extends('layouts.app')

@section('title', 'Semua Produk')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Semua Produk</h1>
        <p class="text-gray-600 max-w-2xl mx-auto">
            Temukan produk berkualitas dari berbagai toko terpercaya. Dapatkan produk terbaik dengan harga yang kompetitif.
        </p>
    </div>

    <!-- Results Info & Controls -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <p class="text-gray-600">
                Menampilkan {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} 
                dari {{ $products->total() }} produk
            </p>
            
            <!-- Active Filters -->
            @if(request()->hasAny(['category', 'condition', 'min_price', 'max_price', 'location', 'search']))
                <div class="flex flex-wrap gap-2 mt-2">
                    @if(request('search'))
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-600 text-white">
                            Pencarian: "{{ request('search') }}"
                            <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="ml-2 text-white/80 hover:text-white">×</a>
                        </span>
                    @endif
                    @if(request('category'))
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-sage-600 text-white">
                            Kategori: {{ $categories->where('slug', request('category'))->first()->name ?? request('category') }}
                            <a href="{{ request()->fullUrlWithQuery(['category' => null]) }}" class="ml-2 text-white/80 hover:text-white">×</a>
                        </span>
                    @endif
                    @if(request('location'))
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-500 text-white">
                            Lokasi: {{ request('location') }}
                            <a href="{{ request()->fullUrlWithQuery(['location' => null]) }}" class="ml-2 text-white/80 hover:text-white">×</a>
                        </span>
                    @endif
                    @if(request('condition'))
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-sage-700 text-white">
                            Kondisi: {{ ucfirst(request('condition')) }}
                            <a href="{{ request()->fullUrlWithQuery(['condition' => null]) }}" class="ml-2 text-white/80 hover:text-white">×</a>
                        </span>
                    @endif
                    @if(request('min_price'))
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-200 text-gray-700">
                            Min: Rp {{ number_format(request('min_price'), 0, ',', '.') }}
                            <a href="{{ request()->fullUrlWithQuery(['min_price' => null]) }}" class="ml-2 text-gray-500 hover:text-gray-700">×</a>
                        </span>
                    @endif
                    @if(request('max_price'))
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-200 text-gray-700">
                            Max: Rp {{ number_format(request('max_price'), 0, ',', '.') }}
                            <a href="{{ request()->fullUrlWithQuery(['max_price' => null]) }}" class="ml-2 text-gray-500 hover:text-gray-700">×</a>
                        </span>
                    @endif
                </div>
            @endif
        </div>
        
        <!-- Filter and Sort Controls -->
        <div class="flex items-center gap-3">
            <button type="button" id="filterButton" class="inline-flex items-center px-4 py-2 bg-sage-600 text-white rounded-lg hover:bg-sage-700 transition-colors gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path></svg>
                Filter
                @if(request()->hasAny(['category', 'condition', 'min_price', 'max_price', 'location']))
                    <span class="bg-white text-sage-600 text-xs px-2 py-1 rounded-full font-medium">
                        {{ collect(request()->only(['category', 'condition', 'min_price', 'max_price', 'location']))->filter()->count() }}
                    </span>
                @endif
            </button>
            
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-600">Urutkan:</label>
                <form action="{{ route('products.index') }}" method="GET" class="inline">
                    @foreach(request()->except('sort') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <select name="sort" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-md focus:ring-sage-500 focus:border-sage-500 text-sm bg-white">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nama A-Z</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga Terendah</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga Tertinggi</option>
                    </select>
                </form>
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    @if($products->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-8">
            @foreach($products as $product)
                <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden group">
                    <a href="{{ route('products.show', $product) }}" class="block">
                        <div class="aspect-square bg-gray-200 relative overflow-hidden">
                            @if($product->main_image)
                                <img src="{{ asset('storage/' . $product->main_image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif
                            @if($product->discount_percentage > 0)
                            <div class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-md">
                                {{ $product->discount_percentage }}%
                            </div>
                            @endif
                        </div>
                        
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 text-sm mb-2 line-clamp-2 h-10">{{ $product->name }}</h3>
                            <div class="mb-2 h-12 flex flex-col justify-center">
                                @if($product->strikethrough_price)
                                    <span class="text-sm text-gray-400 line-through">{{ $product->formatted_strikethrough_price }}</span>
                                @endif
                                <span class="text-lg font-bold text-[#819A91]">{{ $product->formatted_current_price }}</span>
                            </div>
                            
                            <!-- PERBAIKAN: Menampilkan Nama Toko, Lokasi, dan Kondisi -->
                            <div class="text-xs text-gray-500 space-y-1">
                                <p class="truncate flex items-center">
                                     <svg class="w-3 h-3 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                     {{ $product->store->name }}
                                </p>
                                <div class="flex items-center divide-x divide-gray-300">
                                    <span class="pr-2 flex items-center">
                                        <svg class="w-3 h-3 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        {{ $product->store->city ?? 'N/A' }}
                                    </span>
                                    <span class="pl-2">{{ ucfirst($product->condition) }}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <div class="flex justify-center">
            {{ $products->withQueryString()->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <div class="max-w-md mx-auto">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Produk tidak ditemukan</h3>
                <p class="text-gray-500 mb-4">Coba ubah filter atau kata kunci pencarian Anda.</p>
                <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-sage-600 text-white rounded-md hover:bg-sage-700">
                    Lihat Semua Produk
                </a>
            </div>
        </div>
    @endif
</div>

<!-- Filter Modal -->
<div id="filterModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between pb-4 mb-4 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Filter Produk</h3>
                <button id="closeModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form action="{{ route('products.index') }}" method="GET" id="filterForm">
                @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                        <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-sage-500 focus:border-sage-500">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                                    {{ $category->name }} ({{ $category->products_count ?? 0 }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kondisi</label>
                        <select name="condition" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-sage-500 focus:border-sage-500">
                            <option value="">Semua Kondisi</option>
                            <option value="baru" {{ request('condition') == 'baru' ? 'selected' : '' }}>Baru</option>
                            <option value="bekas" {{ request('condition') == 'bekas' ? 'selected' : '' }}>Bekas</option>
                        </select>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi</label>
                        <select name="location" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-sage-500 focus:border-sage-500">
                            <option value="">Semua Lokasi</option>
                            @foreach($locations as $location)
                                <option value="{{ $location }}" {{ request('location') == $location ? 'selected' : '' }}>
                                    {{ $location }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Harga Minimum</label>
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-sage-500 focus:border-sage-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Harga Maximum</label>
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="1000000" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-sage-500 focus:border-sage-500">
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 pt-6 mt-6 border-t">
                    <button type="button" id="resetFilters" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50">Reset Filter</button>
                    <button type="submit" class="px-4 py-2 bg-sage-600 text-white rounded-md hover:bg-sage-700">Terapkan Filter</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterButton = document.getElementById('filterButton');
    const filterModal = document.getElementById('filterModal');
    const closeModal = document.getElementById('closeModal');
    const resetFilters = document.getElementById('resetFilters');
    
    filterButton.addEventListener('click', () => { filterModal.classList.remove('hidden'); });
    closeModal.addEventListener('click', () => { filterModal.classList.add('hidden'); });
    filterModal.addEventListener('click', e => { if (e.target === filterModal) filterModal.classList.add('hidden'); });
    
    resetFilters.addEventListener('click', () => {
        const form = document.getElementById('filterForm');
        form.querySelectorAll('select, input[type="number"]').forEach(el => el.value = '');
        form.submit();
    });
});
</script>
@endpush

<style>
.line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>
@endsection
