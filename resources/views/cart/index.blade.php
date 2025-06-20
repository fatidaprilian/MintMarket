@extends('layouts.app')

@section('title', 'Keranjang Belanja')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Keranjang Belanja</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(count($cartItems) > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Daftar Item Keranjang --}}
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    @foreach($cartItems as $id => $item)
                        <div class="flex flex-col md:flex-row items-center gap-4 {{ !$loop->last ? 'border-b border-gray-200 dark:border-gray-700 pb-6 mb-6' : '' }}">
                            {{-- Gambar Produk --}}
                            <div class="w-24 h-24 flex-shrink-0">
                                @if($item['image'])
                                    <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover rounded-md">
                                @else
                                    <div class="w-full h-full bg-gray-200 rounded-md flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            
                            {{-- Info Produk --}}
                            <div class="flex-grow text-center md:text-left">
                                <h3 class="font-semibold text-lg text-gray-900 dark:text-white mb-2">{{ $item['name'] }}</h3>
                                <p class="text-lg font-bold text-sage-600">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Subtotal: Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</p>
                            </div>
                            
                            {{-- Kontrol Kuantitas dan Hapus --}}
                            <div class="flex items-center gap-4">
                                {{-- Form Update Kuantitas --}}
                                <form action="{{ route('cart.update', $id) }}" method="POST" class="flex items-center gap-2">
                                    @csrf
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Qty:</label>
                                    <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" max="99" 
                                           class="w-16 text-center border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-sage-500 focus:ring-sage-500">
                                    <button type="submit" class="text-sm bg-sage-600 text-white px-3 py-1 rounded hover:bg-sage-700 transition-colors">
                                        Update
                                    </button>
                                </form>
                                
                                {{-- Tombol Hapus --}}
                                <a href="{{ route('cart.remove', $id) }}" 
                                   class="text-red-500 hover:text-red-700 transition-colors"
                                   onclick="return confirm('Yakin ingin menghapus item ini?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Ringkasan Pesanan --}}
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 sticky top-8">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                        Ringkasan Pesanan
                    </h2>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                            <span>Jumlah Item:</span>
                            <span>{{ array_sum(array_column($cartItems, 'quantity')) }} item</span>
                        </div>
                        <div class="flex justify-between font-bold text-lg text-gray-900 dark:text-white border-t border-gray-200 dark:border-gray-700 pt-3">
                            <span>Total Harga:</span>
                            <span class="text-sage-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    
                    <button class="w-full bg-sage-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-sage-700 transition-colors duration-300 mb-3">
                        Lanjut ke Checkout
                    </button>
                    
                    <a href="{{ route('products.index') }}" 
                       class="block w-full text-center border border-sage-600 text-sage-600 font-medium py-3 px-4 rounded-lg hover:bg-sage-50 transition-colors duration-300">
                        Lanjut Belanja
                    </a>
                </div>
            </div>
        </div>
    @else
        {{-- Keranjang Kosong --}}
        <div class="text-center bg-white dark:bg-gray-800 rounded-lg shadow-md p-12">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Keranjang Anda Kosong</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-8">Sepertinya Anda belum menambahkan produk apapun ke keranjang.</p>
            <a href="{{ route('products.index') }}" 
               class="inline-block bg-sage-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-sage-700 transition-colors duration-300">
                Mulai Belanja
            </a>
        </div>
    @endif
</div>
@endsection