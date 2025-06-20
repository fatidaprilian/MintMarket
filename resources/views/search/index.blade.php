@extends('layouts.app')

@section('title', 'Hasil Pencarian: ' . $query)

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Search Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                Hasil Pencarian: "{{ $query }}"
            </h1>
            <p class="text-gray-600">
                Ditemukan {{ number_format($total) }} produk
            </p>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            {{-- Filters Sidebar --}}
            <div class="lg:w-64 flex-shrink-0">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Filter</h3>
                    
                    <form method="GET" action="{{ route('search') }}">
                        <input type="hidden" name="q" value="{{ $query }}">
                        
                        {{-- Category Filter --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                            <select name="category" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $currentCategory == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Price Range --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rentang Harga</label>
                            <div class="flex space-x-2">
                                <input type="number" name="min_price" placeholder="Min" value="{{ $minPrice }}"
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <input type="number" name="max_price" placeholder="Max" value="{{ $maxPrice }}"
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                            </div>
                        </div>

                        {{-- Sort --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Urutkan</label>
                            <select name="sort" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <option value="relevance" {{ $currentSort == 'relevance' ? 'selected' : '' }}>Relevansi</option>
                                <option value="newest" {{ $currentSort == 'newest' ? 'selected' : '' }}>Terbaru</option>
                                <option value="price_low" {{ $currentSort == 'price_low' ? 'selected' : '' }}>Harga Terendah</option>
                                <option value="price_high" {{ $currentSort == 'price_high' ? 'selected' : '' }}>Harga Tertinggi</option>
                                <option value="popular" {{ $currentSort == 'popular' ? 'selected' : '' }}>Terpopuler</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full bg-sage-600 text-white py-2 px-4 rounded-md hover:bg-sage-700 transition-colors">
                            Terapkan Filter
                        </button>
                    </form>
                </div>
            </div>

            {{-- Products Grid --}}
            <div class="flex-1">
                @if($products->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach($products as $product)
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                                <a href="{{ route('products.show', $product->slug ?? $product->id) }}">
                                    {{-- Product Image --}}
                                    <div class="aspect-square bg-gray-100">
                                        @if($product->main_image)
                                            <img src="{{ asset('storage/' . $product->main_image) }}" 
                                                 alt="{{ $product->name }}" 
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <svg class="w-16 h-16 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    {{-- Product Info --}}
                                    <div class="p-4">
                                        <h3 class="font-semibold text-gray-900 truncate mb-2">{{ $product->name }}</h3>
                                        <p class="text-2xl font-bold text-sage-600 mb-2">
                                            Rp {{ number_format($product->price, 0, ',', '.') }}
                                        </p>
                                        <p class="text-sm text-gray-500 truncate">{{ $product->store->name ?? 'Unknown Store' }}</p>
                                        @if($product->category)
                                            <span class="inline-block mt-2 px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded">
                                                {{ $product->category->name }}
                                            </span>
                                        @endif
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-8">
                        {{ $products->links() }}
                    </div>
                @else
                    {{-- No Results --}}
                    <div class="text-center py-16">
                        <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">Tidak ada hasil ditemukan</h3>
                        <p class="mt-2 text-gray-500">Coba ubah kata kunci pencarian atau filter</p>
                        <div class="mt-6">
                            <a href="{{ route('products.index') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-sage-600 hover:bg-sage-700">
                                Lihat Semua Produk
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection