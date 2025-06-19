<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
// Pastikan ini 'CreateRecord', bukan 'create'
use Filament\Resources\Pages\CreateRecord;

// Pastikan ini extends 'CreateRecord', bukan 'create'
class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
