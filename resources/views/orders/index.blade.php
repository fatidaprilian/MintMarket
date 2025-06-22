@extends('layouts.app')

@section('title', 'Pesanan Saya')

@section('content')
<div class="py-6 sm:py-8 lg:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-6 md:p-8 text-gray-900">
                {{-- Header --}}
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Pesanan Saya</h1>
                    <p class="text-gray-600 mt-2">Kelola dan lihat status semua pesanan Anda di sini.</p>
                </div>

                {{-- Menampilkan Notifikasi --}}
                @if(session('success'))
                    <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                @if($orders->isEmpty())
                    <div class="p-8 text-center text-gray-500 bg-gray-50 rounded-lg border border-gray-200">
                        <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        <h3 class="mt-4 text-lg font-semibold text-gray-900">Belum ada pesanan</h3>
                        <p class="mt-2 text-sm text-gray-600">
                            Terlihat Anda belum memiliki riwayat pesanan. Mari mulai jelajahi produk kami!
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('products.index') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-sage-600 hover:bg-sage-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sage-500 transition-colors duration-200">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Jelajahi Produk Sekarang
                            </a>
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-6">
                        @foreach($orders as $order)
                            <div class="bg-sage-50 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 border border-sage-200">
                                {{-- Header Pesanan (Kode, Tanggal, Status) --}}
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 pb-4 border-b border-sage-200">
                                    <div>
                                        <h2 class="text-xl font-bold text-sage-800">
                                            Pesanan <span class="text-sage-900">#{{ $order->transaction_code }}</span>
                                        </h2>
                                        <p class="text-sm text-gray-600 mt-1">
                                            <span class="font-medium">Tanggal Pesan:</span> {{ $order->created_at->format('d F Y, H:i') }}
                                        </p>
                                    </div>
                                    <div class="mt-3 sm:mt-0">
                                        <span class="px-3 py-1 text-sm font-semibold rounded-full 
                                            @if($order->status === 'pending') bg-yellow-200 text-yellow-800
                                            @elseif($order->status === 'paid') bg-blue-200 text-blue-800
                                            @elseif($order->status === 'processing') bg-indigo-200 text-indigo-800
                                            @elseif($order->status === 'shipped') bg-purple-200 text-purple-800
                                            @elseif($order->status === 'delivered') bg-green-200 text-green-800
                                            @elseif($order->status === 'cancelled') bg-red-200 text-red-800
                                            @endif">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Ringkasan Produk dalam Pesanan --}}
                                <div class="space-y-4 mb-4">
                                    <h3 class="font-semibold text-sage-800">Produk dari Toko <span class="text-sage-900">{{ $order->store->name }}</span>:</h3>
                                    @foreach($order->items->take(2) as $item) {{-- Tampilkan maksimal 2 produk --}}
                                    <div class="flex items-center justify-between bg-white p-3 rounded-md border border-gray-200 shadow-sm">
                                        <div class="flex items-center">
                                            <img src="{{ $item->product->main_image }}" 
                                                 alt="{{ $item->product->name }}" 
                                                 class="w-16 h-16 object-cover rounded-md mr-3 border border-gray-200">
                                            <div>
                                                <a href="{{ route('products.show', $item->product->slug) }}" class="font-semibold text-gray-900 hover:text-sage-700 transition-colors">{{ $item->product->name }}</a>
                                                <p class="text-sm text-gray-600 mt-1">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                        <p class="font-bold text-gray-900">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</p>
                                    </div>
                                    @endforeach

                                    @if($order->items->count() > 2)
                                        <p class="text-sm text-gray-500 mt-2 text-right">
                                            + {{ $order->items->count() - 2 }} produk lainnya
                                        </p>
                                    @endif
                                </div>

                                {{-- Total Harga dan Tombol Detail --}}
                                <div class="border-t pt-4 flex flex-col sm:flex-row justify-between items-start sm:items-center">
                                    <div>
                                        <p class="text-gray-600 text-sm">Total Pembayaran:</p>
                                        <p class="text-2xl font-bold text-sage-900 mt-1">
                                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                        </p>
                                    </div>
                                    <a href="{{ route('orders.show', $order->id) }}" class="mt-4 sm:mt-0 inline-flex items-center px-6 py-3 border border-transparent text-base font-bold rounded-lg shadow-sm text-white bg-sage-600 hover:bg-sage-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sage-500 transition-all duration-200 transform hover:scale-105">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Lihat Detail Pesanan
                                    </a>
                                </div>
                            </div>
                        @endforeach

                        {{-- Pagination --}}
                        <div class="mt-8">
                            {{ $orders->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection