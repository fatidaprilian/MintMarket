<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

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
