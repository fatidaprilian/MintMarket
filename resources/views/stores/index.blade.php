@extends('layouts.app')

@section('title', 'Semua Toko')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Semua Toko</h1>
        <p class="text-gray-600 max-w-2xl mx-auto">
            Temukan toko-toko terpercaya dengan berbagai produk berkualitas. 
            Dukung penjual lokal dan dapatkan produk terbaik.
        </p>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
        <form action="{{ route('stores.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari nama toko..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-sage-500 focus:border-sage-500">
            </div>
            <div class="flex gap-2">
                <button type="submit" 
                        class="bg-sage-600 text-white px-6 py-2 rounded-lg hover:bg-sage-700 transition-colors">
                    Cari
                </button>
                @if(request('search'))
                    <a href="{{ route('stores.index') }}" 
                       class="border border-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-50 transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Sort Options -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <p class="text-gray-600">
                Menampilkan {{ $stores->firstItem() ?? 0 }}-{{ $stores->lastItem() ?? 0 }} 
                dari {{ $stores->total() }} toko
            </p>
        </div>
        
        <form action="{{ route('stores.index') }}" method="GET" class="flex items-center gap-2">
            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            <label class="text-sm text-gray-600">Urutkan:</label>
            <select name="sort" onchange="this.form.submit()" 
                    class="px-3 py-2 border border-gray-300 rounded-md focus:ring-sage-500 focus:border-sage-500">
                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nama A-Z</option>
                <option value="products" {{ request('sort') == 'products' ? 'selected' : '' }}>Terbanyak Produk</option>
            </select>
        </form>
    </div>

    <!-- Stores Grid -->
    @if($stores->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($stores as $store)
                <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                    <a href="{{ route('stores.show', $store) }}" class="block">
                        <!-- Store Header -->
                        <div class="bg-gradient-to-r from-sage-100 to-sage-200 p-6">
                            <div class="flex items-center space-x-4">
                                <div class="w-16 h-16 bg-gradient-to-br from-sage-600 to-sage-700 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-xl">{{ substr($store->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900 text-lg">{{ $store->name }}</h3>
                                    <p class="text-sm text-gray-600">{{ $store->city }}, {{ $store->province }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Store Info -->
                        <div class="p-6">
                            @if($store->description)
                                <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ $store->description }}</p>
                            @endif
                            
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center text-gray-500">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                        {{ $store->products_count ?? 0 }} produk
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        {{ $store->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $store->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="flex justify-center">
            {{ $stores->withQueryString()->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <div class="max-w-md mx-auto">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                    @if(request('search'))
                        Toko tidak ditemukan
                    @else
                        Belum Ada Toko
                    @endif
                </h3>
                <p class="text-gray-500 mb-4">
                    @if(request('search'))
                        Coba gunakan kata kunci yang berbeda
                    @else
                        Toko akan segera hadir
                    @endif
                </p>
                @if(request('search'))
                    <a href="{{ route('stores.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-sage-600 text-white rounded-md hover:bg-sage-700 transition-colors">
                        Lihat Semua Toko
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection