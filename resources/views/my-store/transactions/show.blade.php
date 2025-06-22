@extends('layouts.app')
@section('hide_search_bar', true)

@section('title', 'Detail Transaksi #' . $transaction->transaction_code)

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('store.transactions.index') }}" class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-gray-800 mb-4">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Kembali ke Daftar Transaksi
        </a>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Header -->
            <div class="bg-gray-50 p-6 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Detail Pesanan <span class="text-primary-600">#{{ $transaction->transaction_code }}</span></h2>
                        <p class="text-sm text-gray-500 mt-1">Tanggal Pesanan: {{ $transaction->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div class="mt-4 sm:mt-0">
                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-medium {{ $transaction->status_badge['class'] }}">
                            {{ $transaction->status_badge['text'] }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Rincian Pengiriman & Pembayaran -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Shipping Info -->
                    <div>
                        <h3 class="font-semibold text-gray-800 mb-3">Informasi Pengiriman</h3>
                        <div class="space-y-2 text-sm text-gray-600">
                            <p><strong class="font-medium text-gray-800">Nama:</strong> {{ $transaction->user->name }}</p>
                            <p><strong class="font-medium text-gray-800">Telepon:</strong> {{ $transaction->user->phone ?? '-' }}</p>
                            <p><strong class="font-medium text-gray-800">Alamat:</strong> {{ $transaction->user->full_address }}</p>
                             @if($transaction->tracking_number)
                            <p><strong class="font-medium text-gray-800">No. Resi:</strong> <span class="text-blue-600 font-semibold">{{ $transaction->tracking_number }}</span></p>
                            @endif
                        </div>
                    </div>
                    <!-- Payment Info -->
                    <div>
                        <h3 class="font-semibold text-gray-800 mb-3">Rincian Pembayaran</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal Produk</span>
                                <span class="font-medium text-gray-800">Rp {{ number_format($transaction->items->sum(fn($item) => $item->price * $item->quantity), 0, ',', '.') }}</span>
                            </div>
                             <div class="flex justify-between">
                                <span class="text-gray-600">Ongkos Kirim</span>
                                <span class="font-medium text-gray-800">Rp {{ number_format($transaction->shipping_cost, 0, ',', '.') }}</span>
                            </div>
                             <div class="flex justify-between font-bold text-lg text-gray-900 border-t pt-2 mt-2">
                                <span>Total Pembayaran</span>
                                <span>{{ $transaction->formatted_total_amount }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daftar Produk -->
            <div class="p-6 border-t border-gray-200">
                <h3 class="font-semibold text-gray-800 mb-4">Produk yang Dipesan ({{ $transaction->items->sum('quantity') }} item)</h3>
                <div class="space-y-4">
                    @foreach($transaction->items as $item)
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                <img src="{{ $item->product->main_image_url }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ $item->product->name }}</p>
                                <p class="text-sm text-gray-500">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                            </div>
                            <p class="font-semibold text-gray-800">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Action Forms -->
            <div class="bg-gray-50 p-6 border-t border-gray-200">
                
                {{-- Form untuk mengubah status dari Menunggu Pembayaran atau Telah Dibayar -> Diproses --}}
                @if(in_array($transaction->status, ['pending', 'paid']))
                    <h3 class="font-semibold text-gray-800 mb-2">Proses Pesanan</h3>
                    <p class="text-sm text-gray-600 mb-4">Ubah status menjadi "Diproses" untuk menandakan bahwa Anda sudah menerima pembayaran dan sedang menyiapkan pesanan ini.</p>
                    <form action="{{ route('store.transactions.updateStatus', $transaction) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="processing">
                        <div class="text-right">
                            <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-sage-600 hover:bg-sage-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                Proses Pesanan
                            </button>
                        </div>
                    </form>

                {{-- Form untuk mengubah status dari Diproses -> Dikirim --}}
                @elseif($transaction->status == 'processing')
                    <h3 class="font-semibold text-gray-800 mb-2">Kirim Pesanan</h3>
                    <p class="text-sm text-gray-600 mb-4">Masukkan nomor resi untuk mengubah status pesanan menjadi "Dikirim".</p>
                    <form action="{{ route('store.transactions.updateStatus', $transaction) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="shipped">
                        <div>
                            <label for="tracking_number" class="block text-sm font-medium text-gray-700">Nomor Resi</label>
                            <input type="text" name="tracking_number" id="tracking_number" value="{{ old('tracking_number') }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500 @error('tracking_number') border-red-500 @enderror" placeholder="Contoh: JNE1234567890" required>
                            @error('tracking_number')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mt-4 text-right">
                            <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                                <svg class="w-5 h-5 mr-2 -ml-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" /></svg>
                                Konfirmasi & Kirim Pesanan
                            </button>
                        </div>
                    </form>

                {{-- Menampilkan info untuk status lainnya --}}
                @else
                    <h3 class="font-semibold text-gray-800">Informasi Tambahan</h3>
                     @if($transaction->status == 'shipped')
                        <p class="mt-2 text-sm text-gray-600">Pesanan telah dikirim. Menunggu konfirmasi penerimaan dari pembeli.</p>
                     @elseif($transaction->status == 'completed')
                         <p class="mt-2 text-sm text-green-700 font-medium">Pesanan telah selesai.</p>
                     @elseif($transaction->status == 'cancelled')
                         <p class="mt-2 text-sm text-red-700 font-medium">Pesanan telah dibatalkan.</p>
                     @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
