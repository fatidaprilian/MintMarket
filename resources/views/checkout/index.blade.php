@extends('layouts.app')
@section('hide_search_bar', true)

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
                
                @if(isset($isBuyNow) && $isBuyNow)
                    <div class="mb-4 p-4 text-sm text-blue-700 bg-blue-100 rounded-lg flex items-center" role="alert">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <span>Anda sedang melakukan pembelian langsung. <a href="{{ route('cart.index') }}" class="underline font-medium">Periksa keranjang Anda</a> jika ingin menambahkan produk lainnya.</span>
                    </div>
                @endif

                <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
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
                                                @if($user->city || $user->postal_code)
                                                    <p class="text-blue-800 text-sm mt-1">
                                                        {{ $user->city ?? '' }} {{ $user->postal_code ? ', ' . $user->postal_code : '' }}
                                                    </p>
                                                @endif
                                                @if($user->phone)
                                                    <p class="text-blue-800 text-sm mt-1">
                                                        Telepon: {{ $user->phone }}
                                                    </p>
                                                @endif
                                            </div>
                                            <button type="button" onclick="useDefaultAddress()" class="ml-4 bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition-colors">
                                                Gunakan
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="md:col-span-2">
                                        <label for="shipping_address" class="block text-sm font-medium text-gray-700">Alamat Lengkap <span class="text-red-500">*</span></label>
                                        <textarea name="shipping_address" id="shipping_address" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sage-500 focus:ring-sage-500" required placeholder="Contoh: Jl. Merdeka No. 12, RT 01/RW 02, Kel. Sukamaju, Kec. Cibinong">{{ old('shipping_address', $user->address) }}</textarea>
                                        @error('shipping_address')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div>
                                        <label for="city" class="block text-sm font-medium text-gray-700">Kota/Kabupaten <span class="text-red-500">*</span></label>
                                        <select name="city" id="city" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sage-500 focus:ring-sage-500" required>
                                            <option value="">Pilih Kota/Kabupaten...</option>
                                            @if(isset($cities) && !empty($cities))
                                                @foreach($cities as $cityData)
                                                    <option value="{{ $cityData['nama'] }}" {{ old('city', $user->city) == $cityData['nama'] ? 'selected' : '' }}>
                                                        {{ $cityData['nama'] }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('city')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="postal_code" class="block text-sm font-medium text-gray-700">Kode Pos <span class="text-red-500">*</span></label>
                                        <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $user->postal_code) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sage-500 focus:ring-sage-500" required placeholder="Contoh: 12345">
                                        @error('postal_code')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="md:col-span-2">
                                        <label for="phone" class="block text-sm font-medium text-gray-700">Nomor Telepon <span class="text-red-500">*</span></label>
                                        <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sage-500 focus:ring-sage-500" required placeholder="Contoh: 081234567890">
                                        @error('phone')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Checkbox to save address --}}
                                <div class="mt-3">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="save_address" value="1" class="rounded border-gray-300 text-sage-600 shadow-sm focus:border-sage-300 focus:ring focus:ring-sage-200 focus:ring-opacity-50" {{ old('save_address', true) ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-gray-600">Simpan alamat ini untuk pemesanan berikutnya</span>
                                    </label>
                                </div>
                            </div>

                            <h3 class="text-xl font-bold text-sage-800 mt-6 mb-4">2. Opsi Pengiriman & Pembayaran</h3>
                            <div class="bg-sage-50 p-6 rounded-lg space-y-4">
                                <div>
                                    <label for="shipping_method" class="block text-sm font-medium text-gray-700">Metode Pengiriman <span class="text-red-500">*</span></label>
                                    <select name="shipping_method" id="shipping_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sage-500 focus:ring-sage-500" required>
                                        <option value="jne_reg">JNE Reguler (2-3 hari)</option>
                                        <option value="jne_yes">JNE YES (1 hari)</option>
                                        <option value="sicepat_reg">SiCepat Reguler (2-3 hari)</option>
                                        <option value="sicepat_halu">SiCepat HALU (1 hari)</option>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">Biaya pengiriman tetap Rp 15.000 per toko</p>
                                </div>
                                <div>
                                    <label for="payment_method" class="block text-sm font-medium text-gray-700">Metode Pembayaran <span class="text-red-500">*</span></label>
                                    <select name="payment_method" id="payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sage-500 focus:ring-sage-500" required>
                                        <option value="bank_transfer">Transfer Bank (BCA, Mandiri, BRI, BNI)</option>
                                        <option value="e_wallet">E-Wallet (Dana, OVO, GoPay, ShopeePay)</option>
                                        <option value="virtual_account">Virtual Account</option>
                                        <option value="cod">Bayar di Tempat (COD)</option>
                                        <option value="saldo" 
                                            {{ $user->wallet->balance < $total ? 'disabled' : '' }}
                                        >Saldo Dompet ({{ 'Rp ' . number_format($user->wallet->balance, 0, ',', '.') }})</option>
                                    </select>
                                    <p id="saldoInfo" class="text-xs mt-1 {{ $user->wallet->balance < $total ? 'text-red-500' : 'text-green-600' }}">
                                        Saldo Anda: <b>Rp {{ number_format($user->wallet->balance, 0, ',', '.') }}</b>
                                        @if($user->wallet->balance < $total)
                                            (Saldo tidak cukup untuk membayar total pesanan)
                                        @else
                                            (Saldo cukup untuk membayar pesanan)
                                        @endif
                                    </p>
                                </div>
                                
                                <div class="p-3 bg-blue-50 rounded-md mt-2">
                                    <p class="text-sm text-blue-800">
                                        <strong>Info Pembayaran:</strong> Silakan lakukan pembayaran dalam waktu 24 jam setelah memesan. Detail pembayaran akan ditampilkan setelah pesanan berhasil dibuat.
                                    </p>
                                </div>
                            </div>

                            {{-- Ringkasan per Toko --}}
                            <h3 class="text-xl font-bold text-sage-800 mt-6 mb-4">3. Ringkasan Pesanan</h3>
                            @foreach($groupedItems as $storeId => $storeItems)
                                <div class="bg-gray-50 p-6 rounded-lg mb-4 border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                                    <h4 class="font-bold text-lg mb-3 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-sage-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        {{ $storeItems->first()->product->store->name }}
                                    </h4>
                                    <div class="space-y-3">
                                        @foreach($storeItems as $item)
                                            <div class="flex justify-between items-center p-3 bg-white rounded-md border hover:bg-sage-50 transition-colors">
                                                <div class="flex items-center">
                                                    <img src="{{ $item->product->main_image }}" 
                                                         alt="{{ $item->product->name }}" 
                                                         class="w-14 h-14 object-cover rounded-md mr-3">
                                                    <div>
                                                        <p class="font-semibold">{{ $item->product->name }}</p>
                                                        <p class="text-sm text-gray-600">{{ $item->quantity }} x Rp {{ number_format($item->product->current_price, 0, ',', '.') }}</p>
                                                    </div>
                                                </div>
                                                <p class="font-semibold">Rp {{ number_format($item->product->current_price * $item->quantity, 0, ',', '.') }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="border-t mt-4 pt-3">
                                        <div class="flex justify-between text-sm">
                                            <span>Subtotal:</span>
                                            <span>Rp {{ number_format($storeSubtotals[$storeId], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span>Ongkos Kirim:</span>
                                            <span>Rp 15.000</span>
                                        </div>
                                        <div class="flex justify-between font-semibold text-sage-800 pt-1">
                                            <span>Total untuk toko ini:</span>
                                            <span>Rp {{ number_format($storeSubtotals[$storeId] + 15000, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="lg:col-span-1">
                            <div class="bg-white p-6 rounded-lg border shadow-sm sticky top-24">
                                <h3 class="text-xl font-bold text-sage-800 mb-4">Total Pesanan</h3>
                                
                                <div class="space-y-2 mb-4">
                                    <div class="flex justify-between">
                                        <p class="text-gray-600">Subtotal Produk</p>
                                        <p class="font-medium">Rp {{ number_format($subtotal, 0, ',', '.') }}</p>
                                    </div>
                                    <div class="flex justify-between">
                                        <p class="text-gray-600">Ongkos Kirim ({{ count($groupedItems) }} toko)</p>
                                        <p class="font-medium">Rp {{ number_format($shippingCost, 0, ',', '.') }}</p>
                                    </div>
                                    <div class="flex justify-between font-bold text-lg text-sage-800 pt-2 border-t">
                                        <p>Total</p>
                                        <p>Rp {{ number_format($total, 0, ',', '.') }}</p>
                                    </div>
                                </div>

                                <button type="submit" id="orderButton" class="w-full text-white bg-sage-600 hover:bg-sage-700 focus:ring-4 focus:outline-none focus:ring-sage-300 font-bold rounded-lg text-lg px-5 py-3 text-center transition-all hover:scale-105 duration-200 flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Pesan Sekarang
                                </button>
                                
                                <div class="mt-4 flex justify-between items-center text-sage-700">
                                    @if(isset($isBuyNow) && $isBuyNow)
                                        <a href="{{ url()->previous() }}" class="flex items-center text-sm hover:underline">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                            </svg>
                                            Kembali ke Produk
                                        </a>
                                    @else
                                        <a href="{{ route('cart.index') }}" class="flex items-center text-sm hover:underline">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                            </svg>
                                            Kembali ke Keranjang
                                        </a>
                                    @endif
                                    <span class="text-xs bg-sage-100 text-sage-800 p-1 px-2 rounded-md">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ count($cartItems) }} Produk
                                    </span>
                                </div>
                                
                                <div class="mt-4 p-3 bg-gray-50 rounded-md border border-gray-200">
                                    <h4 class="font-medium text-sm text-gray-700 mb-2">Jaminan Belanja</h4>
                                    <ul class="text-xs text-gray-600 space-y-1">
                                        <li class="flex items-start">
                                            <svg class="w-4 h-4 text-green-500 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            <span>Pembayaran Aman 100%</span>
                                        </li>
                                        <li class="flex items-start">
                                            <svg class="w-4 h-4 text-green-500 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            <span>Pengiriman Terjamin</span>
                                        </li>
                                        <li class="flex items-start">
                                            <svg class="w-4 h-4 text-green-500 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            <span>Layanan Pelanggan 24/7</span>
                                        </li>
                                    </ul>
                                </div>
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
    // PERUBAHAN DISINI: Mengatur nilai Select2
    $('#city').val(@json($user->city ?? '')).trigger('change');
    document.getElementById('postal_code').value = @json($user->postal_code ?? '');
    document.getElementById('phone').value = @json($user->phone ?? '');
}

// Script untuk form checkout dan validasi saldo dompet
document.addEventListener('DOMContentLoaded', function() {
    // PERUBAHAN DISINI: Inisialisasi Select2
    $('#city').select2({
        placeholder: 'Pilih atau ketik untuk mencari...',
        // Baris di bawah ini memastikan Select2 bisa di-render dengan benar di atas elemen lain jika diperlukan
        dropdownAutoWidth: true,
        width: '100%'
    });

    const form = document.getElementById('checkoutForm');
    const orderButton = document.getElementById('orderButton');
    const paymentMethodSelect = document.getElementById('payment_method');
    const saldoOption = paymentMethodSelect.querySelector('option[value="saldo"]');
    const saldoUser = {{ $user->wallet->balance }};
    const totalOrder = {{ $total }};

    function toggleSaldoOption() {
        if (saldoUser < totalOrder) {
            saldoOption.disabled = true;
            if (paymentMethodSelect.value === 'saldo') {
                orderButton.disabled = true;
                orderButton.classList.add('opacity-70', 'pointer-events-none');
            }
        } else {
            saldoOption.disabled = false;
            if (paymentMethodSelect.value === 'saldo') {
                orderButton.disabled = false;
                orderButton.classList.remove('opacity-70', 'pointer-events-none');
            }
        }
    }

    paymentMethodSelect.addEventListener('change', function() {
        if (this.value === 'saldo' && saldoUser < totalOrder) {
            orderButton.disabled = true;
            orderButton.classList.add('opacity-70', 'pointer-events-none');
            document.getElementById('saldoInfo').classList.add('text-red-500');
        } else {
            orderButton.disabled = false;
            orderButton.classList.remove('opacity-70', 'pointer-events-none');
            document.getElementById('saldoInfo').classList.remove('text-red-500');
        }
    });

    toggleSaldoOption();

    if (form && orderButton) {
        form.addEventListener('submit', function(event) {
            // Basic validation
            const requiredFields = Array.from(form.querySelectorAll('[required]'));
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('border-red-500');
                    // Tambahkan border merah juga untuk Select2
                    if (field.id === 'city') {
                        $(field).next('.select2-container').find('.select2-selection').css('border-color', '#ef4444');
                    }
                } else {
                    field.classList.remove('border-red-500');
                    if (field.id === 'city') {
                        $(field).next('.select2-container').find('.select2-selection').css('border-color', '#D1D5DB'); // warna border default
                    }
                }
            });
            
            if (!isValid) {
                event.preventDefault();
                alert('Silakan lengkapi semua bidang yang diperlukan.');
                return;
            }
            
            // Disable button to prevent double submission
            orderButton.disabled = true;
            orderButton.classList.add('opacity-70', 'pointer-events-none');
            orderButton.innerHTML = `
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Memproses...
            `;
            
            // Continue with form submission
        });
    }
});
</script>
@endsection