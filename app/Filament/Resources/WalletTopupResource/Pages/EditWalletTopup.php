<?php

namespace App\Filament\Resources\WalletTopupResource\Pages;

use App\Filament\Resources\WalletTopupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWalletTopup extends EditRecord
{
    protected static string $resource = WalletTopupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
