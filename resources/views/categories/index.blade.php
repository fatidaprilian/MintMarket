@extends('layouts.app')

@section('title', 'Semua Kategori')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Semua Kategori</h1>
        <p class="text-gray-600 max-w-2xl mx-auto">
            Jelajahi berbagai kategori produk yang tersedia di marketplace kami. 
            Temukan produk yang sesuai dengan kebutuhan Anda.
        </p>
    </div>

    <!-- Categories Grid -->
    @if($categories && $categories->count() > 0)
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-6">
            @foreach($categories as $category)
                <a href="{{ route('categories.show', $category) }}" 
                   class="group bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow p-6 text-center">
                    <!-- Category Icon -->
                    <div class="w-16 h-16 bg-gradient-to-br from-sage-100 to-sage-200 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:from-sage-200 group-hover:to-sage-300 transition-colors">
                        <svg class="w-8 h-8 text-sage-600" fill="currentColor" viewBox="0 0 20 20">
                            @switch($category->name)
                                @case('Elektronik')
                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                                    @break
                                @case('Fashion')
                                    <path fill-rule="evenodd" d="M7 2a1 1 0 00-.707 1.707L7 4.414v3.758a1 1 0 01-.293.707l-4 4C.817 14.769 2.156 18 4.828 18h10.343c2.673 0 4.012-3.231 2.122-5.121l-4-4A1 1 0 0113 8.172V4.414l.707-.707A1 1 0 0013 2H7zm2 6.172V4h2v4.172a3 3 0 00.879 2.12l1.027 1.028a4 4 0 00-2.171.102l-.47.156a4 4 0 01-2.53 0l-.563-.187a1.993 1.993 0 00-.114-.035l1.063-1.063A3 3 0 009 8.172z" clip-rule="evenodd"></path>
                                    @break
                                @case('Kesehatan & Kecantikan')
                                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                                    @break
                                @case('Hobi & Koleksi')
                                    <path fill-rule="evenodd" d="M10 2L3 7v11a2 2 0 002 2h10a2 2 0 002-2V7l-7-5zM8 15a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                                    @break
                                @case('Olahraga')
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                                    @break
                                @default
                                    <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path>
                            @endswitch
                        </svg>
                    </div>
                    
                    <!-- Category Info -->
                    <h3 class="font-semibold text-gray-900 group-hover:text-sage-600 transition-colors mb-2">
                        {{ $category->name }}
                    </h3>
                    <p class="text-sm text-gray-500">
                        {{ $category->products_count ?? 0 }} produk
                    </p>
                </a>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <div class="max-w-md mx-auto">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14-7l-2 7m2 4l-2 7M5 7l2 7-2 7"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Kategori</h3>
                <p class="text-gray-500">Kategori produk akan segera ditambahkan.</p>
            </div>
        </div>
    @endif
</div>
@endsection