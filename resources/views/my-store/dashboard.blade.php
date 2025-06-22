@extends('layouts.app')

@section('title', 'Dashboard Toko - ' . $store->name)

@push('styles')
<style>
    /* Custom Dashboard Styles */
    .dashboard-card {
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
    }

    .dashboard-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    /* Original gradients for reference */
    /* .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .stat-card-green {
        background: linear-gradient(135deg, #4ade80 0%, #22c55e 100%);
    }

    .stat-card-blue {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    }

    .stat-card-purple {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    }

    .stat-card-orange {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    } */

    .chart-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    @media (max-width: 640px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 rounded-full overflow-hidden bg-gradient-to-br from-primary-500 to-sage-600 flex items-center justify-center">
                        @if($store->logo)
                            <img src="{{ $store->logo_url }}" alt="{{ $store->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-white font-bold text-xl">{{ strtoupper(substr($store->name, 0, 1)) }}</span>
                        @endif
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">
                            Dashboard {{ $store->name }}
                        </h1>
                        <div class="flex items-center space-x-3 mt-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $store->getStatusBadge()['class'] }}">
                                {{ $store->getStatusBadge()['text'] }}
                            </span>
                            <span class="text-sm text-gray-500">{{ $store->city }}, {{ $store->province }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3 mt-4 lg:mt-0">
                    <a href="{{ route('store.edit') }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Profil Toko
                    </a>
                    <a href="{{ route('stores.show', $store) }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm bg-primary-600 text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Lihat Toko
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Penjualan -->
            <div class="dashboard-card bg-green-500 text-white rounded-xl p-6"> {{-- Changed to green-500 --}}
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Total Penjualan</p>
                        <p class="text-2xl font-bold">{{ $store->formatted_total_sales }}</p>
                        <p class="text-green-100 text-xs mt-1">{{ $completedTransactions }} transaksi selesai</p>
                    </div>
                    <div class="p-3 bg-white/20 rounded-lg">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Transaksi -->
            <div class="dashboard-card bg-primary-500 text-white rounded-xl p-6"> {{-- Changed to primary-500 --}}
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-primary-100 text-sm font-medium">Total Transaksi</p>
                        <p class="text-2xl font-bold">{{ $totalTransactions }}</p>
                        <p class="text-primary-100 text-xs mt-1">{{ $pendingTransactions }} pending</p>
                    </div>
                    <div class="p-3 bg-white/20 rounded-lg">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 2L3 7v11a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V7l-7-5z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Produk -->
            <div class="dashboard-card bg-purple-500 text-white rounded-xl p-6"> {{-- Changed to purple-500, assuming you have a purple color in your extended config or using default tailwind purple --}}
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">Total Produk</p>
                        <p class="text-2xl font-bold">{{ $totalProducts }}</p>
                        <p class="text-purple-100 text-xs mt-1">{{ $availableProducts }} tersedia</p>
                    </div>
                    <div class="p-3 bg-white/20 rounded-lg">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732L14.146 12.8l-1.179 4.456a1 1 0 01-1.934 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732L9.854 7.2l1.179-4.456A1 1 0 0112 2z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Rating Toko -->
            <div class="dashboard-card bg-orange-500 text-white rounded-xl p-6"> {{-- Changed to orange-500 --}}
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm font-medium">Rating Toko</p>
                        <p class="text-2xl font-bold">{{ number_format($store->rating ?? 0, 1) }}/5.0</p>
                        <p class="text-orange-100 text-xs mt-1">{{ $totalReviews }} ulasan</p>
                    </div>
                    <div class="p-3 bg-white/20 rounded-lg">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Bar -->
        @if($store->getCompletionPercentage() < 100)
        <div class="bg-white rounded-xl shadow-sm p-6 mb-8 border border-gray-200">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0 w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Lengkapi Profil Toko Anda</h3>
                    <p class="text-gray-600 mb-4">Profil toko yang lengkap dapat meningkatkan kepercayaan pembeli hingga 3x lipat!</p>
                    <div class="w-full bg-gray-200 rounded-full h-3 mb-3">
                        <div class="bg-gradient-to-r from-yellow-400 to-orange-500 h-3 rounded-full transition-all duration-700 ease-out"
                             style="width: {{ $store->getCompletionPercentage() }}%"></div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">
                            <span class="font-bold text-orange-600">{{ $store->getCompletionPercentage() }}%</span> Lengkap
                        </span>
                        <a href="{{ route('store.edit') }}"
                           class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-lg text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                            Lengkapi Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-8 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('store.products.create') }}"
                   class="dashboard-card flex flex-col items-center p-4 rounded-lg border border-gray-200 hover:border-primary-300 transition-colors"> {{-- Changed to primary-300 --}}
                    <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center mb-3"> {{-- Changed to primary-100 --}}
                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"> {{-- Changed to primary-600 --}}
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-900">Tambah Produk</span>
                </a>

                <a href="{{ route('store.transactions.index') }}"
                   class="dashboard-card flex flex-col items-center p-4 rounded-lg border border-gray-200 hover:border-green-300 transition-colors">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-900">Kelola Transaksi</span>
                </a>

                <a href="{{ route('store.analytics') }}"
                   class="dashboard-card flex flex-col items-center p-4 rounded-lg border border-gray-200 hover:border-purple-300 transition-colors">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-900">Analitik</span>
                </a>

                <a href="{{ route('store.promotions') }}"
                   class="dashboard-card flex flex-col items-center p-4 rounded-lg border border-gray-200 hover:border-yellow-300 transition-colors">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-900">Promosi</span>
                </a>
            </div>
        </div>

        <!-- Recent Activities & Products -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Transactions -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Transaksi Terbaru</h3>
                    <a href="{{ route('store.transactions.index') }}" class="text-primary-600 hover:text-primary-800 text-sm font-medium"> {{-- Changed to primary-600/800 --}}
                        Lihat Semua
                    </a>
                </div>

                @if($recentTransactions->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentTransactions as $transaction)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center"> {{-- Changed to primary-100 --}}
                                    <span class="text-primary-600 font-bold text-sm">{{ substr($transaction->transaction_code, -3) }}</span> {{-- Changed to primary-600 --}}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $transaction->user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $transaction->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-medium text-gray-900">{{ $transaction->formatted_total_amount }}</p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $transaction->status_badge['class'] }}">
                                    {{ $transaction->status_badge['text'] }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-500">Belum ada transaksi</p>
                    </div>
                @endif
            </div>

            <!-- Top Products -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Produk Terlaris</h3>
                    <a href="{{ route('store.products.index') }}" class="text-primary-600 hover:text-primary-800 text-sm font-medium"> {{-- Changed to primary-600/800 --}}
                        Lihat Semua
                    </a>
                </div>

                @if($topProducts->count() > 0)
                    <div class="space-y-4">
                        @foreach($topProducts as $product)
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gray-100 rounded-lg overflow-hidden">
                                @if($product->main_image)
                                    <img src="{{ $product->main_image }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 truncate">{{ $product->name }}</p>
                                <p class="text-sm text-gray-500">{{ $product->formatted_current_price }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">{{ $product->stock }} stok</p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->status === 'tersedia' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($product->status) }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <p class="text-gray-500 mb-2">Belum ada produk</p>
                        <a href="{{ route('store.products.create') }}" class="text-primary-600 hover:text-primary-800 text-sm font-medium"> {{-- Changed to primary-600/800 --}}
                            Tambah Produk Pertama
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
