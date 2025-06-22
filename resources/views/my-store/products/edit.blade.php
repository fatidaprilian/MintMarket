@extends('layouts.app')

@section('title', 'Edit Produk - ' . $product->name)

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="bg-white rounded-t-xl shadow-lg overflow-hidden">
            <div class="px-6 py-12 text-center bg-gradient-to-br from-primary-50 to-primary-100">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                    Edit Detail Produk Anda
                </h1>
                <p class="text-lg text-gray-700 max-w-2xl mx-auto">
                    Perbarui informasi produk {{ $product->name }} untuk menjaga data tetap akurat dan menarik bagi pembeli.
                </p>
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
        <form action="{{ route('store.products.update', $product) }}"
              method="POST"
              enctype="multipart/form-data"
              class="bg-white rounded-b-xl shadow-lg"
              x-data="{
                  // Filter null values from existingImages before passing to Alpine.js
                  existingImages: {{ json_encode(array_filter($product->image ?? [])) }},
                  imagePreviews: [],
                  removeImagePaths: [],

                  init() {
                      // Initialize imagePreviews with existing images for display
                      this.imagePreviews = this.existingImages.map(path => '{{ Storage::url('') }}' + path);
                  },

                  handleImageUpload(event) {
                      Array.from(event.target.files).forEach(file => {
                          const reader = new FileReader();
                          reader.onload = (e) => {
                              this.imagePreviews.push(e.target.result);
                          };
                          reader.readAsDataURL(file);
                      });
                  },

                  removeImage(index, isExisting = false, path = null) {
                      if (isExisting && path) {
                          this.removeImagePaths.push(path);
                          this.existingImages.splice(this.existingImages.indexOf(path), 1);
                      }
                      this.imagePreviews.splice(index, 1);
                      // If you need to clear the file input for newly uploaded images
                      // this logic might need refinement depending on single/multiple file input usage
                  }
              }">

            @csrf
            @method('PATCH')
            <input type="hidden" name="remove_images[]" x-for="path in removeImagePaths" :value="path">


            <!-- Section: Informasi Produk -->
            <div class="p-8 border-b border-gray-200">
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0 w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Informasi Produk</h2>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Produk <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="name"
                               name="name"
                               value="{{ old('name', $product->name) }}"
                               required
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 transition duration-150"
                               placeholder="Contoh: Baju Kaos Distro Pria">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        <select id="category_id"
                                name="category_id"
                                required
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ (old('category_id', $product->category_id) == $category->id) ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Deskripsi Produk
                        </label>
                        <textarea id="description"
                                  name="description"
                                  rows="6"
                                  class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 transition duration-150"
                                  placeholder="Jelaskan detail produk Anda, fitur, bahan, ukuran, dll.">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section: Harga & Stok -->
            <div class="p-8 border-b border-gray-200">
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0 w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Harga & Stok</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                            Harga Jual <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               id="price"
                               name="price"
                               value="{{ old('price', $product->price) }}"
                               required
                               min="0"
                               step="0.01"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        @error('price')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="original_price" class="block text-sm font-medium text-gray-700 mb-2">
                            Harga Awal (opsional, untuk diskon)
                        </label>
                        <input type="number"
                               id="original_price"
                               name="original_price"
                               value="{{ old('original_price', $product->original_price) }}"
                               min="0"
                               step="0.01"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        @error('original_price')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">
                            Stok Produk <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               id="stock"
                               name="stock"
                               value="{{ old('stock', $product->stock) }}"
                               required
                               min="0"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        @error('stock')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="condition" class="block text-sm font-medium text-gray-700 mb-2">
                            Kondisi <span class="text-red-500">*</span>
                        </label>
                        <select id="condition"
                                name="condition"
                                required
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Pilih Kondisi</option>
                            <option value="baru" {{ (old('condition', $product->condition) == 'baru') ? 'selected' : '' }}>Baru</option>
                            <option value="bekas" {{ (old('condition', $product->condition) == 'bekas') ? 'selected' : '' }}>Bekas</option>
                        </select>
                        @error('condition')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section: Gambar Produk -->
            <div class="p-8 border-b border-gray-200">
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0 w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Gambar Produk</h2>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="image-upload" class="block text-sm font-medium text-gray-700 mb-2">
                            Unggah Gambar Baru (Maks 5 gambar, max 2MB per gambar)
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:border-primary-400 transition-colors duration-200"
                             @click="document.getElementById('image-upload').click()">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="image-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-primary-600 hover:text-primary-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary-500">
                                        <span>Unggah file</span>
                                        <input id="image-upload" name="image[]" type="file" class="sr-only" multiple accept="image/*" @change="handleImageUpload">
                                    </label>
                                    <p class="pl-1">atau seret dan lepas</p>
                                </div>
                                <p class="text-xs text-gray-500">
                                    PNG, JPG, GIF hingga 2MB
                                </p>
                            </div>
                        </div>

                        {{-- Existing Images Display --}}
                        <template x-if="existingImages.length > 0">
                            <div class="mt-4">
                                <h4 class="text-md font-medium text-gray-700 mb-2">Gambar Saat Ini:</h4>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                                    <template x-for="(path, index) in existingImages" :key="'existing-' + index">
                                        <div class="relative w-full h-32 rounded-lg overflow-hidden border border-gray-300">
                                            <img :src="'{{ Storage::url('') }}' + path" alt="Existing Image" class="w-full h-full object-cover">
                                            <button type="button" @click="removeImage(imagePreviews.indexOf('{{ Storage::url('') }}' + path), true, path)" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 text-xs">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        {{-- New Image Previews --}}
                        <template x-if="imagePreviews.length > existingImages.length">
                            <div class="mt-4">
                                <h4 class="text-md font-medium text-gray-700 mb-2">Preview Gambar Baru:</h4>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                                    <template x-for="(preview, index) in imagePreviews.slice(existingImages.length)" :key="'new-' + index">
                                        <div class="relative w-full h-32 rounded-lg overflow-hidden border border-gray-300">
                                            <img :src="preview" alt="New Image Preview" class="w-full h-full object-cover">
                                            <button type="button" @click="removeImage(existingImages.length + index, false)" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 text-xs">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        @error('image.*')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section: Status & Promo -->
            <div class="p-8 border-b border-gray-200">
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0 w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Status & Promosi</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status Produk <span class="text-red-500">*</span>
                        </label>
                        <select id="status"
                                name="status"
                                required
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500"
                                disabled> {{-- Ditambahkan atribut 'disabled' --}}
                            <option value="tersedia" {{ (old('status', $product->status) == 'tersedia') ? 'selected' : '' }}>Tersedia</option>
                            <option value="habis" {{ (old('status', $product->status) == 'habis') ? 'selected' : '' }}>Habis</option>
                            <option value="menunggu_verifikasi" {{ (old('status', $product->status) == 'menunggu_verifikasi') ? 'selected' : '' }}>Menunggu Verifikasi</option>
                            <option value="terjual" {{ (old('status', $product->status) == 'terjual') ? 'selected' : '' }}>Terjual</option>
                        </select>
                        {{-- Hidden input untuk memastikan nilai status tetap terkirim --}}
                        <input type="hidden" name="status" value="{{ old('status', $product->status) }}">
                        @error('status')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox"
                               id="is_active"
                               name="is_active"
                               value="1"
                               {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                               class="h-5 w-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                        <label for="is_active" class="ml-3 block text-sm font-medium text-gray-700">
                            Aktifkan Produk
                        </label>
                        @error('is_active')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="flash_sale_price" class="block text-sm font-medium text-gray-700 mb-2">
                            Harga Flash Sale (opsional)
                        </label>
                        <input type="number"
                               id="flash_sale_price"
                               name="flash_sale_price"
                               value="{{ old('flash_sale_price', $product->flash_sale_price) }}"
                               min="0"
                               step="0.01"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        @error('flash_sale_price')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="flash_sale_end_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Akhir Flash Sale (opsional)
                        </label>
                        <select id="flash_sale_end_date"
                                name="flash_sale_end_date"
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
                            @foreach($flashSaleSessions as $value => $label)
                                <option value="{{ $value }}" {{ (old('flash_sale_end_date', $selectedFlashSaleDate) == $value) ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('flash_sale_end_date')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="p-8 flex justify-between">
                <button type="button" onclick="window.history.back()"
                        class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-lg shadow-sm text-base font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition duration-150">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Batal
                </button>
                <button type="submit"
                        class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition duration-150">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Update Produk
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
