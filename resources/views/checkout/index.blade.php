@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 md:p-8 text-gray-900">
                {{-- Header --}}
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Checkout</h1>
                    <p class="text-gray-600 mt-2">Lengkapi informasi untuk menyelesaikan pesanan Anda</p>
                </div>

                {{-- Menampilkan Notifikasi --}}
                @if(session('error'))
                    <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                        {{ session('error') }}
                    </div>
                @endif
                @if(session('info'))
                    <div class="mb-4 p-4 text-sm text-blue-700 bg-blue-100 rounded-lg" role="alert">
                        {{ session('info') }}
                    </div>
                @endif

                <form action="{{ route('checkout.process') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                        <div class="lg:col-span-2">
                            <h3 class="text-xl font-bold text-sage-800 mb-4">1. Alamat Pengiriman</h3>
                            <div class="bg-sage-50 p-6 rounded-lg">
                                {{-- Quick Address Selection if user has saved address --}}
                                @if($user->address)
                                    <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <h4 class="font-medium text-blue-900 mb-2">
                                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    </svg>
                                                    Alamat Tersimpan
                                                </h4>
                                                <p class="text-blue-800 text-sm">{{ $user->address }}</p>
                                            </div>
                                            <button type="button" onclick="useDefaultAddress()" class="ml-4 bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition-colors">
                                                Gunakan
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                <label for="shipping_address" class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                                <textarea name="shipping_address" id="shipping_address" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sage-500 focus:ring-sage-500" required placeholder="Contoh: Jl. Merdeka No. 12, RT 01/RW 02, Kel. Sukamaju, Kec. Cibinong, Kab. Bogor, 16914">{{ old('shipping_address', $user->address) }}</textarea>
                                @error('shipping_address')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror

                                {{-- Checkbox to save address --}}
                                @if(!$user->address || old('shipping_address') !== $user->address)
                                    <div class="mt-3">
                                        <label class="flex items-center">
                                            <input type="checkbox" name="save_address" value="1" class="rounded border-gray-300 text-sage-600 shadow-sm focus:border-sage-300 focus:ring focus:ring-sage-200 focus:ring-opacity-50" {{ old('save_address') ? 'checked' : '' }}>
                                            <span class="ml-2 text-sm text-gray-600">Simpan alamat ini untuk pemesanan berikutnya</span>
                                        </label>
                                    </div>
                                @endif
                            </div>

                            <h3 class="text-xl font-bold text-sage-800 mt-6 mb-4">2. Opsi Pengiriman & Pembayaran</h3>
                            <div class="bg-sage-50 p-6 rounded-lg space-y-4">
                                <div>
                                    <label for="shipping_method" class="block text-sm font-medium text-gray-700">Metode Pengiriman</label>
                                    <select name="shipping_method" id="shipping_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sage-500 focus:ring-sage-500" required>
                                        <option value="flat_rate">JNE Reguler (Tarif Tetap)</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="payment_method" class="block text-sm font-medium text-gray-700">Metode Pembayaran</label>
                                    <select name="payment_method" id="payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sage-500 focus:ring-sage-500" required>
                                        <option value="bank_transfer">Transfer Bank Manual</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Ringkasan per Toko --}}
                            <h3 class="text-xl font-bold text-sage-800 mt-6 mb-4">3. Ringkasan Pesanan</h3>
                            @foreach($groupedItems as $storeId => $storeItems)
                                <div class="bg-gray-50 p-6 rounded-lg mb-4">
                                    <h4 class="font-bold text-lg mb-3">{{ $storeItems->first()->product->store->name }}</h4>
                                    <div class="space-y-3">
                                        @foreach($storeItems as $item)
                                            <div class="flex justify-between items-center">
                                                <div class="flex items-center">
                                                    <img src="{{ $item->product->main_image ? asset('storage/' . $item->product->main_image) : 'https://via.placeholder.com/150' }}" 
                                                         alt="{{ $item->product->name }}" 
                                                         class="w-12 h-12 object-cover rounded-md mr-3">
                                                    <div>
                                                        <p class="font-medium">{{ $item->product->name }}</p>
                                                        <p class="text-sm text-gray-600">{{ $item->quantity }} x Rp {{ number_format($item->product->current_price, 0, ',', '.') }}</p>
                                                    </div>
                                                </div>
                                                <p class="font-semibold">Rp {{ number_format($item->product->current_price * $item->quantity, 0, ',', '.') }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="border-t mt-3 pt-3">
                                        <div class="flex justify-between text-sm">
                                            <span>Subtotal:</span>
                                            <span>Rp {{ number_format($storeSubtotals[$storeId], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span>Ongkir:</span>
                                            <span>Rp 15.000</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="lg:col-span-1">
                            <div class="bg-gray-50 p-6 rounded-lg border sticky top-8">
                                <h3 class="text-xl font-bold text-sage-800 mb-4">Total Pesanan</h3>
                                
                                <div class="space-y-2 mb-4">
                                    <div class="flex justify-between">
                                        <p class="text-gray-600">Subtotal</p>
                                        <p class="font-medium">Rp {{ number_format($subtotal, 0, ',', '.') }}</p>
                                    </div>
                                    <div class="flex justify-between">
                                        <p class="text-gray-600">Ongkos Kirim</p>
                                        <p class="font-medium">Rp {{ number_format($shippingCost, 0, ',', '.') }}</p>
                                    </div>
                                    <div class="flex justify-between font-bold text-lg text-sage-800 pt-2 border-t">
                                        <p>Total</p>
                                        <p>Rp {{ number_format($total, 0, ',', '.') }}</p>
                                    </div>
                                </div>

                                <button type="submit" class="w-full text-white bg-sage-600 hover:bg-sage-700 focus:ring-4 focus:outline-none focus:ring-sage-300 font-bold rounded-lg text-lg px-5 py-3 text-center transition-transform transform hover:scale-105">
                                    Buat Pesanan
                                </button>
                                
                                <p class="text-xs text-gray-500 mt-4 text-center">Dengan membuat pesanan, Anda menyetujui Syarat & Ketentuan kami.</p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function useDefaultAddress() {
    document.getElementById('shipping_address').value = @json($user->address ?? '');
}
</script>
@endsection