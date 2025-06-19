<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// 1. Tambahkan dua use statement ini di bagian atas
use App\Http\Responses\LogoutResponse;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // 2. Tambahkan baris ini untuk mendaftarkan response logout kustom Anda
        $this->app->bind(LogoutResponseContract::class, LogoutResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
