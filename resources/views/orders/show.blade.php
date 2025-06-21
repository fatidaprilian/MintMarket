@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->transaction_code)

@section('content')
<div class="py-6 sm:py-8 lg:py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumbs / Back Button --}}
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('orders.index') }}" class="text-sage-700 hover:text-sage-900 flex items-center text-sm font-medium transition-colors duration-200">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Daftar Pesanan
            </a>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Detail Pesanan <span class="text-sage-800">#{{ $order->transaction_code }}</span></h1>
        </div>

        {{-- Notifikasi Umum (Success, Error, Info) --}}
        @if(session('success'))
            <div class="mb-6 p-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                {{ session('error') }}
            </div>
        @endif
        @if(session('info'))
            <div class="mb-6 p-4 text-sm text-blue-700 bg-blue-100 rounded-lg" role="alert">
                {{ session('info') }}
            </div>
        @endif

        {{-- BAGIAN INFORMASI PEMBAYARAN (Pindah ke Atas dan Lebih Menonjol) --}}
        @if($order->status === 'pending')
            <div class="bg-sage-100 p-6 rounded-lg shadow-md mb-6 border border-sage-200">
                <div class="flex items-center mb-4">
                    <svg class="w-6 h-6 mr-3 text-yellow-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h2v-6h-2v6zm0-8h2V7h-2v2z"></path>
                    </svg>
                    <h3 class="font-bold text-xl text-sage-800">Pembayaran Menunggu Konfirmasi</h3>
                </div>
                <p class="text-gray-700 mb-4">Selesaikan pembayaran Anda sebesar <strong class="text-sage-900 text-2xl">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong> dalam waktu 24 jam.</p>
                
                <p class="font-semibold text-sage-700 mb-3">Pilih salah satu rekening tujuan:</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Bank BCA --}}
                    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm" x-data="{ copied: false }">
                        <h4 class="font-bold text-gray-800 mb-2">Bank BCA</h4>
                        <div class="flex justify-between items-center mb-1">
                            <p class="font-mono text-gray-700 text-lg">1234567890</p>
                            <button type="button" @click="navigator.clipboard.writeText('1234567890'); copied = true; setTimeout(() => copied = false, 2000);"
                                    class="ml-2 px-3 py-1 bg-sage-200 text-sage-800 rounded-md text-xs hover:bg-sage-300 transition-colors relative">
                                <span x-show="!copied">Salin</span>
                                <span x-show="copied" class="absolute -top-6 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded whitespace-nowrap z-10 opacity-0 transform translate-y-2 transition-all duration-300" :class="{ 'opacity-100 translate-y-0': copied }">Tersalin!</span>
                            </button>
                        </div>
                        <p class="text-sm text-gray-600">a.n. MintMarket</p>
                    </div>
                    
                    {{-- Bank Mandiri --}}
                    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm" x-data="{ copied: false }">
                        <h4 class="font-bold text-gray-800 mb-2">Bank Mandiri</h4>
                        <div class="flex justify-between items-center mb-1">
                            <p class="font-mono text-gray-700 text-lg">0987654321</p>
                            <button type="button" @click="navigator.clipboard.writeText('0987654321'); copied = true; setTimeout(() => copied = false, 2000);"
                                    class="ml-2 px-3 py-1 bg-sage-200 text-sage-800 rounded-md text-xs hover:bg-sage-300 transition-colors relative">
                                <span x-show="!copied">Salin</span>
                                <span x-show="copied" class="absolute -top-6 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded whitespace-nowrap z-10 opacity-0 transform translate-y-2 transition-all duration-300" :class="{ 'opacity-100 translate-y-0': copied }">Tersalin!</span>
                            </button>
                        </div>
                        <p class="text-sm text-gray-600">a.n. MintMarket</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mt-4">Setelah melakukan pembayaran, silakan hubungi admin untuk konfirmasi pesanan Anda.</p>
            </div>
        @endif

        {{-- MAIN CONTENT --}}
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-6 md:p-8">
                {{-- RINGKASAN STATUS PESANAN (Progress Bar) --}}
                <h3 class="font-bold text-sage-800 text-lg mb-4">Status Pesanan Anda</h3>
                @php
                    // Pastikan urutan status sesuai dengan alur progres
                    $statuses = ['pending', 'paid', 'processing', 'shipped', 'delivered'];
                    $currentStatusIndex = array_search($order->status, $statuses);
                    
                    // Handle 'cancelled' sebagai status non-linear
                    $isCancelled = ($order->status === 'cancelled');
                @endphp
                <div class="flex justify-between items-center relative mb-8">
                    {{-- Garis progress --}}
                    <div class="absolute inset-x-0 top-1/2 h-0.5 bg-gray-200 transform -translate-y-1/2 z-0"></div>
                    
                    @foreach($statuses as $index => $status)
                        <div class="flex flex-col items-center z-10 w-1/5 px-1"> {{-- Bagikan lebar --}}
                            <div class="w-8 h-8 rounded-full flex items-center justify-center 
                                @if($index <= $currentStatusIndex && !$isCancelled) bg-sage-600 text-white
                                @else bg-gray-300 text-gray-600 @endif">
                                @if($index < $currentStatusIndex && !$isCancelled)
                                    {{-- Icon Checkmark for completed steps --}}
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @elseif($index === $currentStatusIndex && !$isCancelled)
                                    {{-- Icon Spinner/Current for current step --}}
                                    <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                @else
                                    {{-- Step number for future steps --}}
                                    {{ $index + 1 }}
                                @endif
                            </div>
                            <span class="mt-2 text-xs font-medium text-center 
                                @if($index === $currentStatusIndex && !$isCancelled) text-sage-700 font-semibold
                                @else text-gray-500 @endif">
                                {{ ucfirst($status) }}
                            </span>
                        </div>
                    @endforeach
                    
                    {{-- Special display for Cancelled status --}}
                    @if($isCancelled)
                        <div class="flex flex-col items-center absolute -bottom-8 left-1/2 -translate-x-1/2 text-red-600 font-semibold">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="mt-1 text-xs">Dibatalkan</span>
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-12">
                    {{-- Informasi Pesanan --}}
                    <div>
                        <h3 class="font-bold text-sage-800 text-lg mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-sage-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                            Detail Transaksi
                        </h3>
                        <div class="bg-gray-50 p-4 rounded-lg text-sm border border-gray-200">
                            <div class="flex justify-between py-1">
                                <span class="font-medium text-gray-700">Kode Transaksi:</span>
                                <span class="text-gray-900 font-semibold">{{ $order->transaction_code }}</span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="font-medium text-gray-700">Tanggal Pesan:</span>
                                <span class="text-gray-900">{{ $order->created_at->format('d F Y, H:i') }}</span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="font-medium text-gray-700">Status:</span>
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full 
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
                            <div class="flex justify-between py-1">
                                <span class="font-medium text-gray-700">Metode Pembayaran:</span>
                                <span class="text-gray-900">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="font-medium text-gray-700">Metode Pengiriman:</span>
                                <span class="text-gray-900">{{ ucfirst(str_replace('_', ' ', $order->shipping_method)) }}</span>
                            </div>
                            {{-- BARIS BARU UNTUK RESI --}}
                            <div class="flex justify-between py-1">
                                <span class="font-medium text-gray-700">Nomor Resi:</span>
                                @if($order->tracking_number)
                                    <span class="text-gray-900 font-semibold">{{ $order->tracking_number }}</span>
                                @else
                                    <span class="text-gray-500 italic">Belum tersedia</span>
                                @endif
                            </div>
                            {{-- AKHIR BARIS BARU UNTUK RESI --}}
                        </div>
                    </div>

                    {{-- Informasi Pengiriman --}}
                    <div>
                        <h3 class="font-bold text-sage-800 text-lg mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-sage-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Alamat Pengiriman
                        </h3>
                        <div class="bg-gray-50 p-4 rounded-lg text-sm border border-gray-200">
                            <p class="font-medium text-gray-700">Penerima:</p>
                            <p class="text-gray-900">{{ $order->user->name ?? 'N/A' }}</p>
                            <p class="font-medium text-gray-700 mt-2">Alamat:</p>
                            <p class="text-gray-900">{!! nl2br(e($order->shipping_address)) !!}</p>
                        </div>
                    </div>
                </div>

                {{-- INFORMASI TOKO DAN PRODUK --}}
                <div class="mt-8">
                    <h3 class="font-bold text-sage-800 text-lg mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-sage-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Produk dari Toko <span class="text-sage-900 ml-1">{{ $order->store->name }}</span>
                    </h3>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="space-y-4">
                            @foreach($order->items as $item)
                                <div class="flex justify-between items-center p-3 bg-white rounded-md border border-gray-200 shadow-sm">
                                    <div class="flex items-center">
                                        <img src="{{ $item->product->main_image ? asset('storage/' . $item->product->main_image) : 'https://via.placeholder.com/80' }}" 
                                             alt="{{ $item->product->name }}" 
                                             class="w-20 h-20 object-cover rounded-md mr-4">
                                        <div>
                                            <a href="{{ route('products.show', $item->product->slug) }}" class="font-semibold text-gray-900 hover:text-sage-700 transition-colors">{{ $item->product->name }}</a>
                                            <p class="text-sm text-gray-600 mt-1">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                    <p class="font-bold text-gray-900">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Total Pembayaran --}}
                <div class="mt-8 p-6 bg-sage-50 rounded-lg shadow-inner border border-sage-200">
                    <h3 class="font-bold text-sage-800 text-lg mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-sage-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Ringkasan Pembayaran
                    </h3>
                    <div class="space-y-2 text-base">
                        <div class="flex justify-between text-gray-700">
                            <span>Subtotal Harga Produk:</span>
                            <span>Rp {{ number_format($order->total_amount - $order->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-gray-700">
                            <span>Ongkos Kirim:</span>
                            <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between font-bold text-xl text-sage-900 border-t pt-3 mt-3">
                            <span>Total Pembayaran:</span>
                            <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Tombol Aksi (Konfirmasi Pembayaran jika Pending) --}}
                @if($order->status === 'pending')
                    <div class="mt-8 flex justify-end">
                        <a href="#" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-bold rounded-lg shadow-sm text-white bg-sage-600 hover:bg-sage-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sage-500 transition-all duration-200 transform hover:scale-105">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            Konfirmasi Pembayaran Sekarang
                        </a>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>

{{-- Pastikan Alpine.js sudah di-include di layout utama Anda (layouts/app.blade.php) --}}
{{-- Contoh: <script src="//unpkg.com/alpinejs" defer></script> --}}
@endsection