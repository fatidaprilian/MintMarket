<div>
    {{-- Loading Overlay --}}
    <div wire:loading.flex class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg flex items-center space-x-3">
            <svg class="animate-spin h-6 w-6 text-sage-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-gray-700 font-medium">Memproses...</span>
        </div>
    </div>

    @if(count($cartItems) > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Daftar Item Keranjang --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                    @foreach($cartItems as $item)
                        <div class="flex flex-col md:flex-row items-start md:items-center gap-6 {{ !$loop->last ? 'border-b border-gray-100 pb-6 mb-6' : '' }} transition-all duration-200 hover:bg-gray-50 rounded-lg p-4 -m-4">
                            {{-- Gambar Produk --}}
                            <div class="w-32 h-32 flex-shrink-0 mx-auto md:mx-0">
                                @if($item['image'])
                                    <img src="{{ asset('storage/' . $item['image']) }}" 
                                         alt="{{ $item['name'] }}" 
                                         class="w-full h-full object-cover rounded-lg shadow-md">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-sage-100 to-sage-200 rounded-lg flex items-center justify-center shadow-md">
                                        <svg class="w-12 h-12 text-sage-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            
                            {{-- Info Produk --}}
                            <div class="flex-grow text-center md:text-left">
                                <h3 class="font-bold text-xl text-gray-900 mb-2">{{ $item['name'] }}</h3>
                                <p class="text-2xl font-bold text-sage-600 mb-2">
                                    Rp {{ number_format($item['price'], 0, ',', '.') }}
                                </p>
                                <p class="text-sm text-gray-600 mb-3">
                                    Subtotal: <span class="font-semibold text-sage-700">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                                </p>
                                @if($item['quantity'] > $item['stock'])
                                    <div class="bg-red-50 border border-red-200 rounded-md p-2 mb-3">
                                        <p class="text-xs text-red-600">⚠️ Stok hanya {{ $item['stock'] }}. Kuantitas disesuaikan.</p>
                                    </div>
                                @endif
                                <div class="flex items-center justify-center md:justify-start">
                                    <span class="text-xs text-gray-600 bg-gray-100 rounded-full px-3 py-1 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $item['store_name'] }}
                                    </span>
                                </div>
                            </div>
                            
                            {{-- Kontrol Kuantitas dan Hapus --}}
                            <div class="flex flex-col items-center gap-4 w-full md:w-auto">
                                {{-- Kontrol Kuantitas --}}
                                <div class="flex items-center gap-2 bg-gray-50 rounded-lg p-2">
                                    {{-- TOMBOL KURANG --}}
                                    <button type="button"
                                            wire:click="decrementQuantity({{ $item['id'] }})"
                                            wire:loading.attr="disabled"
                                            class="w-10 h-10 flex items-center justify-center bg-white border-2 border-gray-200 rounded-lg hover:bg-sage-50 hover:border-sage-300 transition-all duration-200 shadow-sm {{ $item['quantity'] <= 1 ? 'opacity-50 cursor-not-allowed' : 'hover:shadow-md active:scale-95' }}"
                                            {{ $item['quantity'] <= 1 ? 'disabled' : '' }}>
                                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"></path>
                                        </svg>
                                    </button>
                                    
                                    {{-- INPUT QUANTITY --}}
                                    <div class="relative">
                                        <input type="text" 
                                               value="{{ $item['quantity'] }}"
                                               readonly
                                               class="w-16 h-10 text-center text-lg font-bold text-gray-900 bg-white border-2 border-gray-200 rounded-lg focus:border-sage-400 focus:ring-2 focus:ring-sage-200 focus:ring-opacity-50 transition-all duration-200 quantity-input"
                                               style="appearance: textfield; -moz-appearance: textfield; -webkit-appearance: none;">
                                        
                                        {{-- Loading indicator --}}
                                        <div wire:loading.flex 
                                             wire:target="updateQuantity,incrementQuantity,decrementQuantity" 
                                             class="absolute inset-0 bg-white bg-opacity-90 rounded-lg items-center justify-center">
                                            <svg class="w-4 h-4 text-sage-600 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    
                                    {{-- TOMBOL TAMBAH --}}
                                    <button type="button"
                                            wire:click="incrementQuantity({{ $item['id'] }})"
                                            wire:loading.attr="disabled"
                                            class="w-10 h-10 flex items-center justify-center bg-white border-2 border-gray-200 rounded-lg hover:bg-sage-50 hover:border-sage-300 transition-all duration-200 shadow-sm {{ $item['quantity'] >= $item['stock'] ? 'opacity-50 cursor-not-allowed' : 'hover:shadow-md active:scale-95' }}"
                                            {{ $item['quantity'] >= $item['stock'] ? 'disabled' : '' }}>
                                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </button>
                                </div>
                                
                                {{-- Tombol Hapus --}}
                                <button type="button"
                                        wire:click="remove({{ $item['id'] }})"
                                        wire:confirm="Yakin ingin menghapus item ini?"
                                        wire:loading.attr="disabled"
                                        class="flex items-center gap-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg px-4 py-2 transition-all duration-200 text-sm font-medium border border-red-200 hover:border-red-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    <span wire:loading.remove wire:target="remove({{ $item['id'] }})">Hapus</span>
                                    <span wire:loading wire:target="remove({{ $item['id'] }})">Menghapus...</span>
                                </button>
                            </div>
                        </div>
                    @endforeach
                    
                    {{-- Tombol Kosongkan Keranjang --}}
                    <div class="flex justify-end mt-6 pt-6 border-t border-gray-100">
                        <button type="button"
                                wire:click="clearCart"
                                wire:confirm="Yakin ingin mengosongkan seluruh keranjang?"
                                wire:loading.attr="disabled"
                                class="flex items-center gap-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg px-4 py-2 transition-all duration-200 font-medium border border-red-200 hover:border-red-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
                            <span wire:loading.remove wire:target="clearCart">Kosongkan Keranjang</span>
                            <span wire:loading wire:target="clearCart">Mengosongkan...</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Ringkasan Pesanan --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg p-6 sticky top-8 border border-gray-100">
                    <div class="flex items-center gap-2 mb-6">
                        <svg class="w-6 h-6 text-sage-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <h2 class="text-2xl font-bold text-gray-900">Ringkasan Pesanan</h2>
                    </div>
                    
                    <div class="space-y-4 mb-8">
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="font-medium text-gray-700">Jumlah Item:</span>
                            <span class="bg-sage-500 text-white px-3 py-1 rounded-full font-bold text-sm">{{ $totalItems }} item</span>
                        </div>
                        <div class="flex justify-between items-center p-4 bg-sage-50 rounded-lg border-2 border-sage-200">
                            <span class="font-bold text-lg text-gray-900">Total Harga:</span>
                            <span class="text-2xl font-bold text-sage-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <button class="w-full bg-gradient-to-r from-sage-600 to-sage-700 text-white font-bold py-4 px-6 rounded-lg hover:from-sage-700 hover:to-sage-800 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                            Lanjut ke Checkout
                        </button>
                        
                        <a href="{{ route('products.index') }}" 
                           class="block w-full text-center border-2 border-sage-600 text-sage-600 font-bold py-4 px-6 rounded-lg hover:bg-sage-50 hover:border-sage-700 hover:text-sage-700 transition-all duration-300 transform hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Lanjut Belanja
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- Keranjang Kosong --}}
        <div class="text-center bg-white rounded-xl shadow-lg p-16 border border-gray-100">
            <div class="mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-32 w-32 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Keranjang Anda Kosong</h2>
            <p class="text-gray-600 mb-8 text-lg">Sepertinya Anda belum menambahkan produk apapun ke keranjang.</p>
            <a href="{{ route('products.index') }}" 
               class="inline-flex items-center gap-2 bg-gradient-to-r from-sage-600 to-sage-700 text-white font-bold py-4 px-8 rounded-lg hover:from-sage-700 hover:to-sage-800 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 active:scale-95">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Mulai Belanja
            </a>
        </div>
    @endif
</div>