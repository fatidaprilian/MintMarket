<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'MintMarket') }} - @yield('title', 'Marketplace Terpercaya')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    
    @stack('styles') 
</head>
<body class="font-sans antialiased bg-gray-50">
    <nav class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-sage-600 to-sage-700 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-sm">M</span>
                        </div>
                        <span class="text-xl font-bold text-gray-900">MintMarket</span>
                    </a>
                </div>

                {{-- REVISI: Search Bar dibungkus dengan kondisi --}}
                @if(!View::hasSection('hide_search_bar'))
                <div class="flex-1 max-w-lg mx-8 hidden sm:block">
                    @livewire('search-bar')
                </div>
                @endif

                <div class="flex items-center space-x-4">
                    @auth
                        <!-- Tombol Chat -->
                        <a href="{{ route('chat.userChats') }}" class="relative p-2 text-gray-600 hover:text-sage-600 transition-colors" title="Chat">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.77 9.77 0 01-4-.84l-4 1 .86-3.08A7.953 7.953 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            {{-- Badge Unread Chat (Opsional, jika ada fitur unread) --}}
                            {{-- <span class="absolute -top-1 -right-1 bg-primary-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">2</span> --}}
                        </a>

                        <a href="{{ route('cart.index') }}" class="relative p-2 text-gray-600 hover:text-sage-600 transition-colors">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 7H19.5M7 13l-1.5 7m0 0h12"></path>
                            </svg>
                            @php
                                $cartUniqueItemCount = App\Http\Controllers\CartController::getCartItemCount();
                            @endphp
                            @if($cartUniqueItemCount > 0)
                                <span class="absolute -top-1 -right-1 bg-sage-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">{{ $cartUniqueItemCount }}</span>
                            @endif
                        </a>

                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-sage-600 transition-colors">
                                <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center overflow-hidden">
                                    @if(Auth::user()->profile_picture)
                                        <img src="{{ Auth::user()->profile_picture_url }}" alt="{{ Auth::user()->name }}" class="h-full w-full object-cover">
                                    @else
                                        <span class="text-sm font-medium">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                    @endif
                                </div>
                                <span class="hidden md:block">{{ auth()->user()->name }}</span>
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div x-show="open" @click.outside="open = false" x-transition class="absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg py-1 z-50">
                                <div class="px-4 py-2 border-b">
                                    <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500">{{ auth()->user()->getRoleDisplayAttribute() }}</p>
                                </div>
                                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard Pengguna</a>
                                <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Pesanan Saya</a>
                                @if(auth()->user()->hasStore())
                                    <a href="{{ route('store.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Toko Saya</a>
                                @else
                                    <a href="{{ route('store.create') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Buka Toko</a>
                                @endif
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Pengaturan Akun</a>
                                <hr class="my-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-sage-600 transition-colors">Masuk</a>
                        <a href="{{ route('register') }}" class="bg-sage-600 text-white px-4 py-2 rounded-lg hover:bg-sage-700 transition-colors">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main>
        {{ $slot ?? '' }} @yield('content') 
    </main>

    <footer class="bg-gray-900 text-white mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 bg-gradient-to-br from-sage-600 to-sage-700 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-sm">M</span>
                        </div>
                        <span class="text-xl font-bold">MintMarket</span>
                    </div>
                    <p class="text-gray-400">Marketplace terpercaya untuk jual beli online dengan mudah dan aman.</p>
                </div>
                
                <div>
                    <h3 class="font-semibold mb-4">Bantuan</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors">Pusat Bantuan</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Cara Berbelanja</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Cara Berjualan</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="font-semibold mb-4">Tentang</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors">Tentang Kami</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Karir</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Blog</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="font-semibold mb-4">Ikuti Kami</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 01-1.93.07 4.28 4.28 0 004 2.98 8.521 8.521 0 01-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/></svg>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} MintMarket. All rights reserved.</p>
            </div>
        </div>
    </footer>

    @livewireScripts
    @stack('scripts')
</body>
</html>