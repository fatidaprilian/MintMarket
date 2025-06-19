<?php

namespace App\Filament\Resources\StoreResource\Pages;

use App\Filament\Resources\StoreResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStores extends ListRecords
{
    protected static string $resource = StoreResource::class;

    /**
     * Override method ini untuk menghapus tombol "New transaction".
     *
     * @return array
     */
    protected function getHeaderActions(): array
    {
        // Kembalikan array kosong untuk menghilangkan semua aksi di header
        return [];
    }
}
