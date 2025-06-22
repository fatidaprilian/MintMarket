@extends('layouts.app')
@section('hide_search_bar', true)
@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-gradient-to-r from-sage-600 to-sage-700 rounded-lg p-6 md:p-8 mb-8 text-white shadow-lg">
        <div class="flex flex-col md:flex-row items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold">
                    Selamat datang, {{ $user->name }}! 👋 {{-- Menggunakan $user yang dilewatkan dari controller --}}
                </h1>
                <p class="text-sage-100 mt-2 text-sm md:text-base">
                    Kelola akun, pesanan, dan aktivitas Anda di MintMarket dengan mudah.
                </p>
            </div>
            <div class="mt-4 md:mt-0 hidden md:block">
                {{-- Ini bisa diganti dengan avatar profil user jika ada --}}
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center border-2 border-white">
                    <span class="text-3xl font-bold uppercase">{{ substr($user->name, 0, 1) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- Total Pesanan --}}
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-sage-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-sage-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-2xl font-bold text-gray-900">{{ $user->orders_count }}</h3> {{-- Menggunakan $user dari controller --}}
                    <p class="text-gray-600 text-sm">Total Pesanan</p>
                </div>
            </div>
        </div>

        {{-- Pesanan Selesai --}}
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-2xl font-bold text-gray-900">{{ $user->completed_orders_count }}</h3> {{-- Menggunakan $user dari controller --}}
                    <p class="text-gray-600 text-sm">Pesanan Selesai</p>
                </div>
            </div>
        </div>

        {{-- Menunggu Proses --}}
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-2xl font-bold text-gray-900">{{ $user->pending_orders_count }}</h3> {{-- Menggunakan $user dari controller --}}
                    <p class="text-gray-600 text-sm">Menunggu Proses</p>
                </div>
            </div>
        </div>

        {{-- Wishlist (jika ada) --}}
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-2xl font-bold text-gray-900">{{ $user->wishlist_count ?? 0 }}</h3> {{-- Menggunakan $user dari controller --}}
                    <p class="text-gray-600 text-sm">Wishlist</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Aksi Cepat</h2>
            <div class="space-y-3">
                <a href="{{ route('profile.edit') }}" 
                   class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    <div class="w-10 h-10 bg-sage-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-sage-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="font-medium text-gray-900">Edit Profil</h3>
                        <p class="text-sm text-gray-600">Update informasi pribadi Anda</p>
                    </div>
                </a>

                <a href="{{ route('orders.index') }}" {{-- Link ke halaman orders.index --}}
                   class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    <div class="w-10 h-10 bg-sage-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-sage-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="font-medium text-gray-900">Riwayat Pesanan</h3>
                        <p class="text-sm text-gray-600">Lihat semua pesanan Anda</p>
                    </div>
                </a>

                {{-- Link untuk Buat Toko / Kelola Toko --}}
                @php
                    $hasStore = $user->hasStore(); // Menggunakan metode hasStore() dari objek user yang sudah dimuat
                @endphp
                <a href="{{ $hasStore ? route('store.index') : route('store.create') }}" 
                   class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    <div class="w-10 h-10 bg-sage-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-sage-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="font-medium text-gray-900">{{ $hasStore ? 'Kelola Toko' : 'Buat Toko' }}</h3>
                        <p class="text-sm text-gray-600">{{ $hasStore ? 'Atur produk & pesanan toko Anda' : 'Mulai berjualan di MintMarket' }}</p>
                    </div>
                </a>

                <a href="#" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    <div class="w-10 h-10 bg-sage-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-sage-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="font-medium text-gray-900">Bantuan</h3>
                        <p class="text-sm text-gray-600">FAQ dan dukungan pelanggan</p>
                    </div>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center justify-between">
                Aktivitas Pesanan Terbaru
                <a href="{{ route('orders.index') }}" class="text-sm text-sage-600 hover:text-sage-700 font-medium transition-colors">Lihat Semua</a>
            </h2>
            <div class="space-y-4">
                @forelse($recentOrders as $order)
                    <div class="flex items-start p-3 bg-gray-50 rounded-lg shadow-sm border border-gray-100">
                        <img src="{{ $order->items->first()->product->main_image ? asset('storage/' . $order->items->first()->product->main_image) : 'https://via.placeholder.com/50' }}"
                             alt="{{ $order->items->first()->product->name ?? 'Produk' }}"
                             class="w-14 h-14 object-cover rounded-md mr-4">
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 text-sm">
                                Pesanan <a href="{{ route('orders.show', $order->id) }}" class="text-sage-700 hover:underline">#{{ $order->transaction_code }}</a>
                            </p>
                            <p class="text-gray-700 text-sm mt-0.5">
                                Total: <span class="font-semibold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </p>
                            <p class="text-gray-600 text-xs mt-1">
                                {{ $order->created_at->diffForHumans() }} - 
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full 
                                    @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->status === 'paid') bg-blue-100 text-blue-800
                                    @elseif($order->status === 'processing') bg-indigo-100 text-indigo-800
                                    @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                                    @elseif($order->status === 'delivered') bg-green-100 text-green-800
                                    @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </p>
                            @if($order->items->count() > 1)
                                <p class="text-xs text-gray-500 mt-1">
                                    dan {{ $order->items->count() - 1 }} produk lainnya
                                </p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Aktivitas</h3>
                        <p class="text-gray-600 mb-4">Mulai berbelanja untuk melihat aktivitas pesanan terbaru Anda di sini.</p>
                        <a href="{{ route('products.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-sage-600 text-white rounded-md hover:bg-sage-700 transition-colors">
                            Mulai Belanja
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection