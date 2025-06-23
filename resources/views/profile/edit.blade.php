@extends('layouts.app')
@section('hide_search_bar', true)

@section('title', 'Edit Profil')

@section('content')
<div class="min-h-screen bg-sage-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header Section (Tanpa input file dan tanpa foto profil) --}}
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-sage-900">{{ Auth::user()->name }}</h1>
            <p class="text-sage-600 mt-2">Kelola informasi profil dan alamat Anda</p>
        </div>

        {{-- Navigation Tabs --}}
        <div class="bg-white rounded-xl shadow-sm mb-8 overflow-hidden border border-sage-100">
            <div class="border-b border-sage-100">
                <nav class="flex space-x-8 px-6" aria-label="Tabs">
                    <button class="tab-button active border-b-2 border-sage-500 py-4 px-1 text-sm font-medium text-sage-600 transition-colors duration-200" data-tab="profile">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Informasi Profil
                    </button>
                    <button class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-sage-500 hover:text-sage-700 hover:border-sage-300 transition-colors duration-200" data-tab="security">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Keamanan
                    </button>
                    <button class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-sage-500 hover:text-sage-700 hover:border-sage-300 transition-colors duration-200" data-tab="account">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Pengaturan Akun
                    </button>
                </nav>
            </div>
        </div>

        {{-- Tab Contents --}}
        <div class="space-y-6">
            {{-- Profile Information Tab --}}
            <div id="profile-tab" class="tab-content active">
                <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-sage-100">
                    <div class="bg-gradient-to-r from-sage-100 to-sage-200 px-6 py-4 border-b border-sage-200">
                        <h3 class="text-lg font-semibold text-sage-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-sage-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Informasi Personal
                        </h3>
                        <p class="text-sm text-sage-700 mt-1">Update informasi dasar dan alamat Anda</p>
                    </div>
                    
                    <form method="post" action="{{ route('profile.update') }}" class="p-6" enctype="multipart/form-data">
                        @csrf
                        @method('patch')

                        {{-- AVATAR PROFILE PICTURE INPUT DAN PREVIEW (HANYA SATU) --}}
                        <div class="mb-6 flex justify-center">
                            <div class="relative inline-block">
                                <div class="w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg ring-4 ring-sage-200 overflow-hidden" id="profile-picture-preview-container">
                                    @php
                                        $disk = env('FILE_STORAGE_DISK', 'public');
                                        $profilePicture = Auth::user()->profile_picture;
                                    @endphp
                                    @if($profilePicture)
                                        @if($disk === 'vercel_blob')
                                            <img src="{{ env('VERCEL_BLOB_URL') . '/' . $profilePicture }}" alt="Profile Picture" class="w-full h-full object-cover">
                                        @else
                                            <img src="{{ asset('storage/' . $profilePicture) }}" alt="Profile Picture" class="w-full h-full object-cover">
                                        @endif
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-sage-600 to-sage-700 flex items-center justify-center">
                                            <span class="text-3xl font-bold text-white">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                </div>
                                <label for="profile_picture_upload" class="absolute -bottom-2 -right-2 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow-lg border-2 border-sage-100 cursor-pointer hover:bg-gray-100 transition-colors">
                                    <svg class="w-4 h-4 text-sage-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                    <input type="file" id="profile_picture_upload" name="profile_picture" class="hidden" accept="image/*">
                                </label>
                            </div>
                        </div>
                        {{-- END AVATAR PROFILE PICTURE INPUT DAN PREVIEW --}}

                        {{-- Error display for profile_picture --}}
                        @error('profile_picture')
                            <p class="text-red-500 text-xs mb-4 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Nama --}}
                            <div class="space-y-2">
                                <label for="name" class="block text-sm font-medium text-sage-800">
                                    <svg class="w-4 h-4 inline mr-1 text-sage-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Nama Lengkap
                                </label>
                                <input id="name" name="name" type="text" 
                                        class="w-full px-4 py-3 border border-sage-300 rounded-lg focus:ring-2 focus:ring-sage-500 focus:border-sage-500 transition-all duration-200 bg-white hover:border-sage-400" 
                                        value="{{ old('name', $user->name) }}" 
                                        required autofocus
                                        placeholder="Masukkan nama lengkap Anda">
                                @error('name')
                                    <p class="text-red-500 text-xs mt-1 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="space-y-2">
                                <label for="email" class="block text-sm font-medium text-sage-800">
                                    <svg class="w-4 h-4 inline mr-1 text-sage-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                    </svg>
                                    Email Address
                                </label>
                                <input id="email" name="email" type="email" 
                                        class="w-full px-4 py-3 border border-sage-300 rounded-lg focus:ring-2 focus:ring-sage-500 focus:border-sage-500 transition-all duration-200 bg-white hover:border-sage-400" 
                                        value="{{ old('email', $user->email) }}" 
                                        required
                                        placeholder="nama@email.com">
                                @error('email')
                                    <p class="text-red-500 text-xs mt-1 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                                
                                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                    <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                        <div class="flex items-start">
                                            <svg class="w-4 h-4 text-yellow-600 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-2.694-.833-3.464 0L3.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                            </svg>
                                            <div>
                                                <p class="text-sm text-yellow-800 font-medium">Email belum terverifikasi</p>
                                                <p class="text-sm text-yellow-700 mt-1">
                                                    <button form="send-verification" class="underline text-yellow-600 hover:text-yellow-800 font-medium transition-colors">
                                                        Kirim ulang email verifikasi
                                                    </button>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Phone --}}
                        <div class="space-y-2 mt-6">
                            <label for="phone" class="block text-sm font-medium text-sage-800">
                                <svg class="w-4 h-4 inline mr-1 text-sage-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                Nomor Telepon
                            </label>
                            <input id="phone" name="phone" type="text" 
                                   class="w-full px-4 py-3 border border-sage-300 rounded-lg focus:ring-2 focus:ring-sage-500 focus:border-sage-500 transition-all duration-200 bg-white hover:border-sage-400" 
                                   value="{{ old('phone', $user->phone) }}" 
                                   placeholder="Contoh: 08123456789">
                            @error('phone')
                                <p class="text-red-500 text-xs mt-1 flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Address (Alamat Lengkap) --}}
                        <div class="space-y-2 mt-6">
                            <label for="address" class="block text-sm font-medium text-sage-800">
                                <svg class="w-4 h-4 inline mr-1 text-sage-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Alamat Lengkap
                            </label>
                            <textarea id="address" name="address" rows="4" 
                                        class="w-full px-4 py-3 border border-sage-300 rounded-lg focus:ring-2 focus:ring-sage-500 focus:border-sage-500 transition-all duration-200 bg-white hover:border-sage-400 resize-none" 
                                        placeholder="Contoh: Jl. Merdeka No. 12, RT 01/RW 02, Kel. Sukamaju">{{ old('address', $user->address) }}</textarea>
                            @error('address')
                                <p class="text-red-500 text-xs mt-1 flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- NEW: City and Postal Code --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            {{-- City --}}
                            <div class="space-y-2">
                                <label for="city" class="block text-sm font-medium text-sage-800">
                                    <svg class="w-4 h-4 inline mr-1 text-sage-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Kota/Kabupaten
                                </label>
                                <input id="city" name="city" type="text" 
                                        class="w-full px-4 py-3 border border-sage-300 rounded-lg focus:ring-2 focus:ring-sage-500 focus:border-sage-500 transition-all duration-200 bg-white hover:border-sage-400" 
                                        value="{{ old('city', $user->city) }}" 
                                        placeholder="Contoh: Bogor">
                                @error('city')
                                    <p class="text-red-500 text-xs mt-1 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Postal Code --}}
                            <div class="space-y-2">
                                <label for="postal_code" class="block text-sm font-medium text-sage-800">
                                    <svg class="w-4 h-4 inline mr-1 text-sage-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Kode Pos
                                </label>
                                <input id="postal_code" name="postal_code" type="text" 
                                        class="w-full px-4 py-3 border border-sage-300 rounded-lg focus:ring-2 focus:ring-sage-500 focus:border-sage-500 transition-all duration-200 bg-white hover:border-sage-400" 
                                        value="{{ old('postal_code', $user->postal_code) }}" 
                                        placeholder="Contoh: 16912">
                                @error('postal_code')
                                    <p class="text-red-500 text-xs mt-1 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                        {{-- END NEW: City and Postal Code --}}

                        <div class="flex items-start p-3 bg-sage-50 rounded-lg border border-sage-200 mt-6">
                            <svg class="w-4 h-4 mt-0.5 mr-2 text-sage-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-xs text-sage-700">
                                <span class="font-medium">Tips:</span> Alamat ini akan digunakan secara otomatis saat checkout untuk mempermudah proses pemesanan Anda.
                            </p>
                        </div>

                        {{-- Submit Button --}}
                        <div class="flex items-center justify-between mt-8 pt-6 border-t border-sage-200">
                            @if (session('status') === 'profile-updated')
                                <div class="flex items-center text-green-600 bg-green-50 px-3 py-2 rounded-lg border border-green-200" 
                                    x-data="{ show: true }" 
                                    x-show="show" 
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 transform scale-95"
                                    x-transition:enter-end="opacity-100 transform scale-100"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100 transform scale-100"
                                    x-transition:leave-end="opacity-0 transform scale-95"
                                    x-init="setTimeout(() => show = false, 4000)">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-sm font-medium">Profil berhasil diperbarui!</span>
                                </div>
                            @else
                                <div></div>
                            @endif
                            
                            <button type="submit" 
                                    class="bg-gradient-to-r from-sage-600 to-sage-700 text-white px-6 py-3 rounded-lg font-medium hover:from-sage-700 hover:to-sage-800 focus:ring-4 focus:ring-sage-300 transition-all duration-200 flex items-center shadow-sm hover:shadow-md transform hover:-translate-y-0.5">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Security Tab --}}
            <div id="security-tab" class="tab-content hidden">
                <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-blue-100">
                    <div class="bg-gradient-to-r from-blue-100 to-blue-200 px-6 py-4 border-b border-blue-200">
                        <h3 class="text-lg font-semibold text-blue-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            Keamanan Akun
                        </h3>
                        <p class="text-sm text-blue-700 mt-1">Kelola password dan keamanan akun Anda</p>
                    </div>
                    <div class="p-6">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </div>

            {{-- Account Settings Tab --}}
            <div id="account-tab" class="tab-content hidden">
                <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-red-100">
                    <div class="bg-gradient-to-r from-red-100 to-red-200 px-6 py-4 border-b border-red-200">
                        <h3 class="text-lg font-semibold text-red-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-2.694-.833-3.464 0L3.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            Pengaturan Akun
                        </h3>
                        <p class="text-sm text-red-700 mt-1">Kelola pengaturan akun dan penghapusan data</p>
                    </div>
                    <div class="p-6">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Hidden forms --}}
<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const targetTab = button.getAttribute('data-tab');

            // Remove active class from all buttons and contents
            tabButtons.forEach(btn => {
                btn.classList.remove('active', 'border-sage-500', 'text-sage-600');
                btn.classList.add('border-transparent', 'text-sage-500');
            });
            
            tabContents.forEach(content => {
                content.classList.remove('active');
                content.classList.add('hidden');
            });

            // Add active class to clicked button and corresponding content
            button.classList.add('active', 'border-sage-500', 'text-sage-600');
            button.classList.remove('border-transparent', 'text-sage-500');
            
            const targetContent = document.getElementById(targetTab + '-tab');
            if (targetContent) {
                targetContent.classList.add('active');
                targetContent.classList.remove('hidden');
            }
        });
    });

    // Add input focus effects
    const inputs = document.querySelectorAll('input, textarea');
    inputs.forEach(input => {
        input.addEventListener('focus', () => {
            input.classList.add('transform', '-translate-y-0.5', 'shadow-lg');
        });
        
        input.addEventListener('blur', () => {
            input.classList.remove('transform', '-translate-y-0.5', 'shadow-lg');
        });
    });

    // JavaScript untuk live preview gambar profil (hanya satu preview)
    const profilePictureUpload = document.getElementById('profile_picture_upload');
    const profilePicturePreviewContainer = document.getElementById('profile-picture-preview-container');

    if (profilePictureUpload && profilePicturePreviewContainer) {
        profilePictureUpload.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Update preview container
                    profilePicturePreviewContainer.innerHTML = `<img src="${e.target.result}" alt="Profile Picture Preview" class="w-full h-full object-cover">`;
                };
                reader.readAsDataURL(file);
            } else {
                // Jika tidak ada file dipilih, kembalikan ke inisial avatar
                // Atau tampilkan gambar profil lama jika ada
                @php
                    $disk = env('FILE_STORAGE_DISK', 'public');
                    $profilePicture = Auth::user()->profile_picture;
                    $defaultAvatarHtml = '<div class="w-full h-full bg-gradient-to-br from-sage-600 to-sage-700 flex items-center justify-center"><span class="text-3xl font-bold text-white">' . substr($user->name, 0, 1) . '</span></div>';
                    $userProfilePictureUrl = null;
                    if ($profilePicture) {
                        if ($disk === 'vercel_blob') {
                            $userProfilePictureUrl = env('VERCEL_BLOB_URL') . '/' . $profilePicture;
                        } else {
                            $userProfilePictureUrl = asset('storage/' . $profilePicture);
                        }
                    }
                @endphp

                if ("{{ $userProfilePictureUrl }}") {
                    profilePicturePreviewContainer.innerHTML = `<img src="{{ $userProfilePictureUrl }}" alt="Profile Picture" class="w-full h-full object-cover">`;
                } else {
                    profilePicturePreviewContainer.innerHTML = `{!! $defaultAvatarHtml !!}`;
                }
            }
        });
    }
});
</script>
@endpush
@endsection