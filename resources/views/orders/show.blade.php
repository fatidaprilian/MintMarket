@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->transaction_code)

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Detail Pesanan #{{ $order->transaction_code }}</h1>
            <p class="text-gray-600 mt-2">Informasi lengkap pesanan Anda</p>
        </div>
        
        @if(session('success'))
        <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
            <span class="font-medium">Pesanan Berhasil!</span> {{ session('success') }}
            <div class="mt-3 p-3 bg-white rounded border">
                <p class="font-medium">Informasi Pembayaran:</p>
                <p class="mt-1">Silakan lakukan pembayaran sejumlah <strong>Rp {{ number_format($order->total_amount, 0,',','.') }}</strong> ke rekening berikut:</p>
                <div class="mt-2 font-mono text-sm bg-gray-100 p-2 rounded">
                    <p><strong>Bank BCA</strong></p>
                    <p>No. Rekening: 1234567890</p>
                    <p>Atas Nama: MintMarket</p>
                </div>
                <p class="text-xs text-gray-600 mt-2">Setelah melakukan pembayaran, silakan hubungi admin untuk konfirmasi.</p>
            </div>
        </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-bold text-sage-800 mb-3">Informasi Pesanan</h3>
                        <div class="space-y-2">
                            <p><strong>Kode:</strong> {{ $order->transaction_code }}</p>
                            <p><strong>Tanggal:</strong> {{ $order->created_at->format('d F Y, H:i') }}</p>
                            <p><strong>Status:</strong> 
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    @if($order->status === 'pending') bg-yellow-200 text-yellow-800
                                    @elseif($order->status === 'paid') bg-blue-200 text-blue-800
                                    @elseif($order->status === 'processing') bg-indigo-200 text-indigo-800
                                    @elseif($order->status === 'shipped') bg-purple-200 text-purple-800
                                    @elseif($order->status === 'delivered') bg-green-200 text-green-800
                                    @elseif($order->status === 'cancelled') bg-red-200 text-red-800
                                    @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </p>
                            <p><strong>Toko:</strong> {{ $order->store->name }}</p>
                            <p><strong>Metode Pembayaran:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-bold text-sage-800 mb-3">Alamat Pengiriman</h3>
                        <div class="bg-gray-50 p-3 rounded">
                            <p class="text-sm">{{ $order->shipping_address }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="border-t my-6"></div>

                <h3 class="font-bold text-sage-800 mb-4">Rincian Produk</h3>
                <div class="space-y-4">
                     @foreach($order->items as $item)
                    <div class="flex justify-between items-center p-4 rounded-md border hover:bg-sage-50">
                        <div class="flex items-center">
                            <img src="{{ $item->product->main_image ? asset('storage/' . $item->product->main_image) : 'https://via.placeholder.com/150' }}" 
                                 alt="{{ $item->product->name }}" 
                                 class="w-16 h-16 object-cover rounded-md mr-4">
                            <div>
                                <p class="font-semibold">{{ $item->product->name }}</p>
                                <p class="text-sm text-gray-600">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <p class="font-semibold">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</p>
                    </div>
                    @endforeach
                </div>

                <div class="border-t my-6"></div>

                <div class="flex justify-end">
                    <div class="w-64">
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span>Subtotal:</span>
                                <span>Rp {{ number_format($order->total_amount - $order->shipping_cost, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Ongkos Kirim:</span>
                                <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between font-bold text-lg border-t pt-2">
                                <span>Total:</span>
                                <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between mt-8">
                    <a href="{{ route('orders.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition-colors">
                        ← Kembali ke Daftar Pesanan
                    </a>
                    
                    @if($order->status === 'pending')
                    <div class="text-sm text-gray-600">
                        <p>Pesanan menunggu pembayaran. Silakan lakukan pembayaran sesuai instruksi di atas.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection