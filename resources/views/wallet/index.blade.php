@extends('layouts.app')
@section('title', 'Dompet Saya')
@section('hide_search_bar', true)

@section('content')
<div class="max-w-3xl mx-auto py-10 px-4">
    {{-- Heading --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-1">Dompet Saya</h1>
            <div class="text-gray-500">Kelola saldo dan transaksi walletmu dengan mudah.</div>
        </div>
        <a href="{{ route('wallet.topup.form') }}"
            class="inline-flex items-center px-5 py-2 bg-gradient-to-r from-emerald-500 to-emerald-700 shadow-md text-white rounded-lg font-semibold hover:scale-105 transition-transform duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Top Up
        </a>
    </div>

    {{-- Saldo --}}
    <div class="bg-gradient-to-r from-emerald-100 to-emerald-50 rounded-2xl shadow-lg p-7 mb-8 flex items-center justify-between ring-1 ring-emerald-200">
        <div>
            <div class="text-base text-emerald-700 font-bold">Saldo Wallet</div>
            <div class="text-4xl font-extrabold text-emerald-700 mt-1 mb-1 tracking-tight">
                Rp {{ number_format($wallet?->balance ?? 0, 0, ',', '.') }}
            </div>
            <div class="text-xs text-gray-500 mt-1">Saldo dapat digunakan untuk transaksi di MintMarket.</div>
        </div>
        <img src="https://cdn-icons-png.flaticon.com/512/4139/4139981.png" class="w-20 h-20 opacity-80" alt="Wallet Icon">
    </div>

    {{-- Notifikasi sukses --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-emerald-100 border border-emerald-200 rounded text-emerald-700 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Riwayat Permintaan Top Up --}}
    <div class="mb-10">
        <h2 class="font-semibold text-lg text-gray-900 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path d="M12 8v4l3 3" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="12" cy="12" r="10" stroke="currentColor"/>
            </svg>
            Permintaan Top Up Terbaru
        </h2>
        @if($topups && $topups->count())
            <div class="overflow-x-auto rounded-lg shadow ring-1 ring-gray-200">
            <table class="min-w-full bg-white text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-600 font-semibold">Tanggal</th>
                        <th class="px-4 py-3 text-left text-gray-600 font-semibold">Nominal</th>
                        <th class="px-4 py-3 text-left text-gray-600 font-semibold">Status</th>
                        <th class="px-4 py-3 text-left text-gray-600 font-semibold">Bukti</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topups as $topup)
                        <tr class="border-t">
                            <td class="px-4 py-3">{{ $topup->created_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3 font-semibold text-emerald-600">Rp {{ number_format($topup->amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                                    @if($topup->status === 'pending') bg-yellow-100 text-yellow-700
                                    @elseif($topup->status === 'approved') bg-emerald-100 text-emerald-700
                                    @elseif($topup->status === 'rejected') bg-red-100 text-red-700
                                    @endif
                                ">
                                    {{ ucfirst($topup->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($topup->proof)
                                    <a href="{{ asset('storage/' . $topup->proof) }}" target="_blank"
                                        class="underline text-emerald-700 hover:text-emerald-900">Lihat</a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        @else
            <div class="text-gray-500 text-sm italic mt-2">Belum ada permintaan top up.</div>
        @endif
    </div>

    {{-- Riwayat Transaksi Wallet --}}
    <div>
        <h2 class="font-semibold text-lg text-gray-900 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path d="M17 9V7a5 5 0 0 0-10 0v2" />
                <rect x="5" y="11" width="14" height="10" rx="2" />
                <path d="M8 21v1m8-1v1" />
            </svg>
            Riwayat Transaksi Wallet
        </h2>
        @if($transactions && $transactions->count())
            <div class="overflow-x-auto rounded-lg shadow ring-1 ring-gray-200">
            <table class="min-w-full bg-white text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-600 font-semibold">Tanggal</th>
                        <th class="px-4 py-3 text-left text-gray-600 font-semibold">Tipe</th>
                        <th class="px-4 py-3 text-left text-gray-600 font-semibold">Nominal</th>
                        <th class="px-4 py-3 text-left text-gray-600 font-semibold">Deskripsi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $trx)
                        <tr class="border-t">
                            <td class="px-4 py-3">{{ $trx->created_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3">
                                @if($trx->type === 'credit')
                                    <span class="inline-flex items-center px-2 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-semibold">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path d="M5 12l5 5L20 7" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        Masuk
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path d="M19 12l-5 5L4 7" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        Keluar
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-semibold {{ $trx->type === 'credit' ? 'text-emerald-600' : 'text-red-600' }}">
                                Rp {{ number_format($trx->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3">{{ $trx->description ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
            {{-- Pagination --}}
            <div class="mt-4">
                {{ $transactions->links() }}
            </div>
        @else
            <div class="text-gray-500 text-sm italic">Belum ada transaksi wallet.</div>
        @endif
    </div>
</div>
@endsection