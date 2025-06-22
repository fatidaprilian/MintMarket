@extends('layouts.app')

@section('title', 'Kelola Transaksi - ' . $store->name)

@push('styles')
<style>
    .status-tab.active {
        border-bottom-color: #819a91; /* sage-600 */
        color: #5a7565; /* sage-800 */
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50" x-data="{ autoProcess: {{ $store->auto_process_orders ? 'true' : 'false' }} }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Tombol Kembali ke Dashboard -->
        <a href="{{ route('store.index') }}" class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-gray-800 mb-4">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Kembali ke Dashboard
        </a>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            
            <!-- Header dan Toggle Otomatis -->
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Kelola Transaksi</h1>
                    <p class="text-sm text-gray-500 mt-1">Lihat dan kelola semua pesanan yang masuk ke toko Anda.</p>
                </div>
                <div class="flex items-center space-x-3 mt-4 sm:mt-0 p-3 bg-gray-50 border rounded-lg">
                    <span class="text-sm font-medium text-gray-700" id="toggle-label">
                        Proses Otomatis Pesanan Baru
                    </span>
                    <form action="{{ route('store.toggleAutoProcess') }}" method="POST" class="inline-block">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            class="relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                            :class="autoProcess ? 'bg-primary-600' : 'bg-gray-200'"
                            role="switch" :aria-checked="autoProcess.toString()" aria-labelledby="toggle-label">
                            <span aria-hidden="true"
                                class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"
                                :class="autoProcess ? 'translate-x-5' : 'translate-x-0'"></span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Status Tabs -->
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-6 overflow-x-auto">
                    <a href="{{ route('store.transactions.index', ['status' => 'all']) }}" class="status-tab whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ request('status', 'all') == 'all' ? 'active' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Semua <span class="ml-1 bg-gray-200 text-gray-600 rounded-full px-2 py-0.5 text-xs">{{ $statusCounts['all'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route('store.transactions.index', ['status' => 'pending']) }}" class="status-tab whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ request('status') == 'pending' ? 'active' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Menunggu Pembayaran <span class="ml-1 bg-yellow-200 text-yellow-800 rounded-full px-2 py-0.5 text-xs">{{ $statusCounts['pending'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route('store.transactions.index', ['status' => 'processing']) }}" class="status-tab whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ request('status') == 'processing' ? 'active' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Perlu Dikirim <span class="ml-1 bg-blue-200 text-blue-800 rounded-full px-2 py-0.5 text-xs">{{ $statusCounts['processing'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route('store.transactions.index', ['status' => 'shipped']) }}" class="status-tab whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ request('status') == 'shipped' ? 'active' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Dikirim <span class="ml-1 bg-purple-200 text-purple-800 rounded-full px-2 py-0.5 text-xs">{{ $statusCounts['shipped'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route('store.transactions.index', ['status' => 'completed']) }}" class="status-tab whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ request('status') == 'completed' ? 'active' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Selesai <span class="ml-1 bg-green-200 text-green-800 rounded-full px-2 py-0.5 text-xs">{{ $statusCounts['completed'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route('store.transactions.index', ['status' => 'cancelled']) }}" class="status-tab whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ request('status') == 'cancelled' ? 'active' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Dibatalkan <span class="ml-1 bg-red-200 text-red-800 rounded-full px-2 py-0.5 text-xs">{{ $statusCounts['cancelled'] ?? 0 }}</span>
                    </a>
                </nav>
            </div>

            <!-- Daftar Transaksi -->
            <div class="space-y-4 mt-6">
                @forelse($transactions as $transaction)
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 hover:shadow-md transition-shadow duration-300">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="font-semibold text-primary-600">#{{ $transaction->transaction_code }}</p>
                                <p class="text-sm text-gray-600">Pembeli: <span class="font-medium">{{ $transaction->user->name }}</span></p>
                                <p class="text-xs text-gray-500">{{ $transaction->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            <div class="mt-4 sm:mt-0 sm:text-right">
                                <p class="text-sm text-gray-600">Total Pesanan</p>
                                <p class="font-bold text-lg text-gray-900">{{ $transaction->formatted_total_amount }}</p>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-200 flex items-center justify-between">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $transaction->status_badge['class'] }}">
                                {{ $transaction->status_badge['text'] }}
                            </span>
                            <a href="{{ route('store.transactions.show', $transaction) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c.51 0 .962-.343 1.087-.835l.383-1.437M7.5 14.25L5.106 5.165A2.25 2.25 0 002.854 3H2.25" /></svg>
                        </div>
                        <p class="text-gray-500">Tidak ada transaksi dengan status ini.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $transactions->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
