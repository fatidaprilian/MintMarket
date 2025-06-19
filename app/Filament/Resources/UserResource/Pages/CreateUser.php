<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
// Pastikan ini 'CreateRecord', bukan 'create'
use Filament\Resources\Pages\CreateRecord;

// Pastikan ini extends 'CreateRecord', bukan 'create'
class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
