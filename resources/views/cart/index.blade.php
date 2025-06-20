@extends('layouts.app')

@section('title', 'Keranjang Belanja')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Keranjang Belanja</h1>
            <p class="text-gray-600">Kelola produk yang ingin Anda beli</p>
        </div>

        {{-- Livewire Cart Component --}}
        @livewire('cart')
    </div>
</div>
@endsection