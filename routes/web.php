<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\MyStoreController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

// Public routes (untuk tamu - bisa akses tanpa login)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/stores', [StoreController::class, 'index'])->name('stores.index');
Route::get('/stores/{store:slug}', [StoreController::class, 'show'])->name('stores.show');
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/search', [SearchController::class, 'index'])->name('search');

// Auth routes (Breeze)
require __DIR__ . '/auth.php';

// Protected routes (butuh login)
Route::middleware(['auth', 'verified'])->group(function () {
    // User Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Cart Management (butuh login)
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/add/{product}', [CartController::class, 'add'])->name('add');
        Route::patch('/update/{product}', [CartController::class, 'update'])->name('update');
        Route::delete('/remove/{product}', [CartController::class, 'remove'])->name('remove');
        Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
    });

    // Checkout & Orders
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/process', [CheckoutController::class, 'process'])->name('process');
    });

    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
    });

    // Store Management (1 user bisa buat/kelola toko)
    Route::prefix('my-store')->name('store.')->group(function () {
        Route::get('/', [MyStoreController::class, 'index'])->name('index');
        Route::get('/create', [MyStoreController::class, 'create'])->name('create');
        Route::post('/', [MyStoreController::class, 'store'])->name('store');
        Route::get('/edit', [MyStoreController::class, 'edit'])->name('edit');
        Route::patch('/update', [MyStoreController::class, 'update'])->name('update');

        // Store Products Management
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', [MyStoreController::class, 'products'])->name('index');
            Route::get('/create', [MyStoreController::class, 'createProduct'])->name('create');
            Route::post('/', [MyStoreController::class, 'storeProduct'])->name('store');
            Route::get('/{product}/edit', [MyStoreController::class, 'editProduct'])->name('edit');
            Route::patch('/{product}', [MyStoreController::class, 'updateProduct'])->name('update');
            Route::delete('/{product}', [MyStoreController::class, 'destroyProduct'])->name('destroy');
        });

        // Store Orders Management
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [MyStoreController::class, 'orders'])->name('index');
            Route::get('/{order}', [MyStoreController::class, 'showOrder'])->name('show');
            Route::patch('/{order}/status', [MyStoreController::class, 'updateOrderStatus'])->name('status');
        });

        // Store Analytics
        Route::get('/analytics', [MyStoreController::class, 'analytics'])->name('analytics');
    });
});
