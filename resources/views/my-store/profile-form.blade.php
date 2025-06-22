@extends('layouts.app')
@section('hide_search_bar', true)

@section('title', ($isCreating ? 'Buka Toko Baru' : 'Edit Profil Toko') . ' - MintMarket')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="bg-white rounded-t-xl shadow-lg overflow-hidden">
            <div class="px-6 py-12 text-center bg-gradient-to-br from-primary-50 to-primary-100">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                    {{ $isCreating ? 'Wujudkan Toko Impian Anda!' : 'Kelola Toko Anda dengan Mudah' }}
                </h1>
                <p class="text-lg text-gray-700 max-w-2xl mx-auto">
                    {{ $isCreating ? 'Isi detail toko Anda di MintMarket dan mulai jangkau pembeli baru.' : 'Perbarui informasi toko Anda, pastikan selalu akurat dan menarik bagi pelanggan.' }}
                </p>

                @if(!$isCreating && $store)
                <!-- Progress Bar -->
                <div class="mt-8 max-w-md mx-auto">
                    <p class="text-sm font-medium text-gray-700 mb-3">Kelengkapan Profil Toko</p>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-primary-600 h-3 rounded-full transition-all duration-700 ease-out" 
                             style="width: {{ $store->getCompletionPercentage() }}%"></div>
                    </div>
                    <p class="mt-2 text-sm text-gray-600">
                        <span class="font-bold text-primary-700">{{ $store->getCompletionPercentage() }}%</span> Lengkap
                    </p>
                </div>
                @endif
            </div>
        </div>

        <!-- Notifications -->
        @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition 
             x-init="setTimeout(() => show = false, 5000)"
             class="fixed top-4 right-4 z-50 max-w-sm">
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 shadow-lg">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-green-800">Berhasil!</h3>
                        <p class="mt-1 text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                    <button @click="show = false" class="ml-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg p-1.5 hover:bg-green-100">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        @endif

        @if (session('error'))
        <div x-data="{ show: true }" x-show="show" x-transition 
             x-init="setTimeout(() => show = false, 5000)"
             class="fixed top-4 right-4 z-50 max-w-sm">
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 shadow-lg">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Terjadi Kesalahan!</h3>
                        <p class="mt-1 text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                    <button @click="show = false" class="ml-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg p-1.5 hover:bg-red-100">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        @endif

        @if ($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan pada form:</h3>
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <!-- Main Form -->
        <form action="{{ $isCreating ? route('store.store') : route('store.update') }}" 
              method="POST" 
              enctype="multipart/form-data" 
              class="bg-white rounded-b-xl shadow-lg"
              x-data="{
                  operatingHours: {{ json_encode(old('operating_hours', $operatingHours)) }},
                  logoFile: null,
                  bannerFile: null,
                  logoPreviewUrl: '{{ $store && $store->logo ? Storage::url($store->logo) : asset('images/default-store-logo.png') }}',
                  bannerPreviewUrl: '{{ $store && $store->banner ? Storage::url($store->banner) : asset('images/default-store-banner.jpg') }}',
                  removeLogoFlag: false,
                  removeBannerFlag: false,

                  init() {
                      const nameInput = document.getElementById('name');
                      if (nameInput) {
                          nameInput.addEventListener('input', (event) => {
                              const slugInput = document.getElementById('slug');
                              if (slugInput) {
                                  slugInput.value = event.target.value.toLowerCase()
                                      .replace(/[^a-z0-9 -]/g, '')
                                      .replace(/\s+/g, '-')
                                      .replace(/-+$/, '');
                              }
                          });
                      }
                  },

                  handleLogoChange(event) {
                      const file = event.target.files[0];
                      if (file) {
                          this.logoFile = file;
                          this.logoPreviewUrl = URL.createObjectURL(file);
                          this.removeLogoFlag = false;
                      }
                  },

                  removeLogo() {
                      this.logoFile = null;
                      this.logoPreviewUrl = '{{ asset('images/default-store-logo.png') }}';
                      this.removeLogoFlag = true;
                      document.getElementById('logo-input').value = '';
                  },

                  handleBannerChange(event) {
                      const file = event.target.files[0];
                      if (file) {
                          this.bannerFile = file;
                          this.bannerPreviewUrl = URL.createObjectURL(file);
                          this.removeBannerFlag = false;
                      }
                  },

                  removeBanner() {
                      this.bannerFile = null;
                      this.bannerPreviewUrl = '{{ asset('images/default-store-banner.jpg') }}';
                      this.removeBannerFlag = true;
                      document.getElementById('banner-input').value = '';
                  },

                  addOperatingHour() {
                      this.operatingHours.push({ day: '', open: '', close: '' });
                  },

                  removeOperatingHour(index) {
                      this.operatingHours.splice(index, 1);
                  }
              }">
            
            @csrf
            @if(!$isCreating)
                @method('PATCH')
                <input type="hidden" name="remove_logo" x-model="removeLogoFlag">
                <input type="hidden" name="remove_banner" x-model="removeBannerFlag">
            @endif

            <!-- Section: Informasi Dasar -->
            <div class="p-8 border-b border-gray-200">
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0 w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Informasi Dasar Toko</h2>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Toko <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $store->name ?? '') }}" 
                               required
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 transition duration-150"
                               placeholder="Contoh: Toko Barokah Jaya">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                            URL Toko (Slug)
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   id="slug" 
                                   name="slug" 
                                   value="{{ old('slug', $store->slug ?? '') }}" 
                                   disabled
                                   class="block w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg cursor-not-allowed">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Slug akan otomatis dibuat dari nama toko Anda</p>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Deskripsi Toko
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="4" 
                                  class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 transition duration-150"
                                  placeholder="Jelaskan tentang toko Anda, produk unggulan, filosofi, atau keunikan toko Anda...">{{ old('description', $store->description ?? '') }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section: Logo & Banner -->
            <div class="p-8 border-b border-gray-200">
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0 w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Logo & Banner Toko</h2>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Logo Upload -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Logo Toko</label>
                        <p class="text-sm text-gray-500 mb-4">Rekomendasi: Gambar persegi, format PNG/JPG, ukuran max 1MB</p>
                        
                        <div class="relative">
                            <div class="w-40 h-40 mx-auto border-2 border-dashed border-gray-300 rounded-full overflow-hidden group hover:border-primary-400 transition-colors duration-200">
                                <img :src="logoPreviewUrl" 
                                     alt="Logo Preview" 
                                     class="w-full h-full object-cover">
                                <input type="file" 
                                       id="logo-input" 
                                       name="logo" 
                                       accept="image/*"
                                       @change="handleLogoChange"
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            </div>
                            
                            <div class="mt-4 text-center">
                                <button type="button" 
                                        @click="document.getElementById('logo-input').click()"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    Pilih Logo
                                </button>
                                
                                <template x-if="logoPreviewUrl !== '{{ asset('images/default-store-logo.png') }}'">
                                    <button type="button" 
                                            @click="removeLogo"
                                            class="ml-2 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        Hapus
                                    </button>
                                </template>
                            </div>
                        </div>
                        @error('logo')
                            <p class="mt-2 text-sm text-red-600 text-center">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Banner Upload -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Banner Toko</label>
                        <p class="text-sm text-gray-500 mb-4">Rekomendasi: Rasio 3:1 (1200x400px), format PNG/JPG, max 2MB</p>
                        
                        <div class="relative">
                            <div class="w-full h-32 border-2 border-dashed border-gray-300 rounded-lg overflow-hidden group hover:border-primary-400 transition-colors duration-200">
                                <img :src="bannerPreviewUrl" 
                                     alt="Banner Preview" 
                                     class="w-full h-full object-cover">
                                <input type="file" 
                                       id="banner-input" 
                                       name="banner" 
                                       accept="image/*"
                                       @change="handleBannerChange"
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            </div>
                            
                            <div class="mt-4 text-center">
                                <button type="button" 
                                        @click="document.getElementById('banner-input').click()"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    Pilih Banner
                                </button>
                                
                                <template x-if="bannerPreviewUrl !== '{{ asset('images/default-store-banner.jpg') }}'">
                                    <button type="button" 
                                            @click="removeBanner"
                                            class="ml-2 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        Hapus
                                    </button>
                                </template>
                            </div>
                        </div>
                        @error('banner')
                            <p class="mt-2 text-sm text-red-600 text-center">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section: Alamat Toko -->
            <div class="p-8 border-b border-gray-200">
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0 w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Alamat Toko</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="province" class="block text-sm font-medium text-gray-700 mb-2">
                            Provinsi <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="province" 
                               name="province" 
                               value="{{ old('province', $store->province ?? '') }}" 
                               required
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500"
                               placeholder="Contoh: Jawa Barat">
                        @error('province')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                            Kota <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="city" 
                               name="city" 
                               value="{{ old('city', $store->city ?? '') }}" 
                               required
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500"
                               placeholder="Contoh: Bandung">
                        @error('city')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                            Alamat Lengkap <span class="text-red-500">*</span>
                        </label>
                        <textarea id="address" 
                                  name="address" 
                                  rows="3" 
                                  required
                                  class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500"
                                  placeholder="Jalan, Nomor, RT/RW, Kelurahan, Kecamatan">{{ old('address', $store->address ?? '') }}</textarea>
                        @error('address')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">
                            Kode Pos <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="postal_code" 
                               name="postal_code" 
                               value="{{ old('postal_code', $store->postal_code ?? '') }}" 
                               required
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500"
                               placeholder="Contoh: 40111">
                        @error('postal_code')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section: Kontak & Sosial Media -->
            <div class="p-8 border-b border-gray-200">
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0 w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Kontak & Sosial Media</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon Toko</label>
                        <input type="text" 
                               id="phone" 
                               name="phone" 
                               value="{{ old('phone', $store->phone ?? '') }}"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500"
                               placeholder="Contoh: 0221234567">
                        @error('phone')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="whatsapp" class="block text-sm font-medium text-gray-700 mb-2">Nomor WhatsApp</label>
                        <input type="text" 
                               id="whatsapp" 
                               name="whatsapp" 
                               value="{{ old('whatsapp', $store->whatsapp ?? '') }}"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500"
                               placeholder="Contoh: 6281234567890">
                        @error('whatsapp')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="email_store" class="block text-sm font-medium text-gray-700 mb-2">Email Toko</label>
                        <input type="email" 
                               id="email_store" 
                               name="email_store" 
                               value="{{ old('email_store', $store->email ?? '') }}"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500"
                               placeholder="toko@example.com">
                        @error('email_store')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="instagram" class="block text-sm font-medium text-gray-700 mb-2">Instagram</label>
                        <input type="url" 
                               id="instagram" 
                               name="instagram" 
                               value="{{ old('instagram', $store->instagram ?? '') }}"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500"
                               placeholder="https://instagram.com/username">
                        @error('instagram')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="facebook" class="block text-sm font-medium text-gray-700 mb-2">Facebook</label>
                        <input type="url" 
                               id="facebook" 
                               name="facebook" 
                               value="{{ old('facebook', $store->facebook ?? '') }}"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500"
                               placeholder="https://facebook.com/username">
                        @error('facebook')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tiktok" class="block text-sm font-medium text-gray-700 mb-2">TikTok</label>
                        <input type="url" 
                               id="tiktok" 
                               name="tiktok" 
                               value="{{ old('tiktok', $store->tiktok ?? '') }}"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500"
                               placeholder="https://tiktok.com/@username">
                        @error('tiktok')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section: Informasi Tambahan -->
            <div class="p-8 border-b border-gray-200">
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0 w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Informasi Tambahan</h2>
                </div>

                <div class="space-y-6">
                    <div>
                        <label for="store_type" class="block text-sm font-medium text-gray-700 mb-2">Tipe Toko</label>
                        <select id="store_type" 
                                name="store_type"
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Pilih Tipe Toko</option>
                            <option value="personal" {{ (old('store_type', $store->store_type ?? '') == 'personal') ? 'selected' : '' }}>Personal</option>
                            <option value="official" {{ (old('store_type', $store->store_type ?? '') == 'official') ? 'selected' : '' }}>Official Store</option>
                            <option value="reseller" {{ (old('store_type', $store->store_type ?? '') == 'reseller') ? 'selected' : '' }}>Reseller</option>
                        </select>
                        @error('store_type')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jam Operasional -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jam Operasional</label>
                        <div class="space-y-3">
                            <template x-for="(hour, index) in operatingHours" :key="index">
                                <div class="flex flex-col sm:flex-row gap-3 p-4 bg-gray-50 rounded-lg border">
                                    <input type="text" 
                                           :name="'operating_hours[' + index + '][day]'" 
                                           x-model="hour.day"
                                           placeholder="Hari (contoh: Senin)"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500">
                                    <input type="text" 
                                           :name="'operating_hours[' + index + '][open]'" 
                                           x-model="hour.open"
                                           placeholder="Jam buka (09:00)"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500">
                                    <input type="text" 
                                           :name="'operating_hours[' + index + '][close]'" 
                                           x-model="hour.close"
                                           placeholder="Jam tutup (17:00)"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500">
                                    <button type="button" 
                                            @click="removeOperatingHour(index)"
                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        Hapus
                                    </button>
                                </div>
                            </template>
                        </div>
                        <button type="button" 
                                @click="addOperatingHour"
                                class="mt-3 inline-flex items-center px-4 py-2 border border-primary-300 rounded-md shadow-sm text-sm font-medium text-primary-700 bg-primary-50 hover:bg-primary-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Tambah Jam Operasional
                        </button>
                    </div>

                    <div>
                        <label for="terms_and_conditions" class="block text-sm font-medium text-gray-700 mb-2">
                            Syarat & Ketentuan Toko
                        </label>
                        <textarea id="terms_and_conditions" 
                                  name="terms_and_conditions" 
                                  rows="6"
                                  class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500"
                                  placeholder="Kebijakan pengembalian, garansi, atau informasi penting lainnya...">{{ old('terms_and_conditions', $store->terms_and_conditions ?? '') }}</textarea>
                        @error('terms_and_conditions')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section: Status Toko (Edit Only) -->
            @if (!$isCreating && $store)
            <div class="p-8 border-b border-gray-200">
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0 w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Status Toko</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-8 h-8 text-yellow-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Rating Toko</h3>
                                <p class="text-2xl font-bold text-gray-900">{{ number_format($store->rating, 1) }}/5.0</p>
                                <p class="text-sm text-gray-500">Berdasarkan ulasan pembeli</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-lg">
                        <div class="flex items-center">
                            @if ($store->is_verified)
                                <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Status Verifikasi</h3>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        Terverifikasi
                                    </span>
                                </div>
                            @else
                                <div class="flex-shrink-0 w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.298 2.09-1.298 2.855 0l.793 1.342c.155.264.435.42.732.41l1.474-.074c1.324-.067 2.253.884 2.039 2.087l-.321 1.804c-.042.238.075.478.287.596l1.256.699c1.128.629 1.128 2.195 0 2.824l-1.256.699c-.212.118-.329.358-.287.596l.321 1.804c.214 1.203-.715 2.154-2.039 2.087l-1.474-.074c-.297-.01-.577.146-.732.41l-.793 1.342c-.765 1.298-2.09 1.298-2.855 0l-.793-1.342c-.155-.264-.435-.42-.732-.41l-1.474.074c-1.324.067-2.253-.884-2.039-2.087l.321-1.804c.042-.238-.075-.478-.287-.596l-1.256-.699c-1.128-.629-1.128-2.195 0-2.824l1.256-.699c.212-.118.329-.358.287-.596l-.321-1.804c-.214-1.203.715-2.154 2.039-2.087l1.474.074c.297.01.577-.146.732-.41l.793-1.342zm2.03 5.018a1 1 0 00-1.414-1.414L10 7.586 8.707 6.293a1 1 0 00-1.414 1.414L8.586 9l-1.293 1.293a1 1 0 101.414 1.414L10 10.414l1.293 1.293a1 1 0 001.414-1.414L11.414 9l1.293-1.293z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Status Verifikasi</h3>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                        Belum Terverifikasi
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Submit Button -->
            <div class="p-8">
                <div class="flex justify-end">
                    <button type="submit" 
                            class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition duration-150">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        {{ $isCreating ? 'Buka Toko Sekarang' : 'Update Profil Toko' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection