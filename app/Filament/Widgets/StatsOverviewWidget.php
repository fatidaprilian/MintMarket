<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\User;
use App\Models\Store;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Permintaan Verifikasi', Product::where('status', 'menunggu_verifikasi')->count())
                ->description('Jumlah produk yang perlu diverifikasi')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Total Products', Product::query()->count())
                ->description('Jumlah semua produk di marketplace')
                ->descriptionIcon('heroicon-m-bolt')
                ->color('success'),

            Stat::make('Total Toko', Store::where('is_active', true)->count())
                ->description('Jumlah toko / penjual aktif')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('info'),

            Stat::make('Total Users', User::where('role', 'user')->count())
                ->description('Jumlah user terdaftar di marketplace')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
        ];
    }
}
