<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WalletResource\Pages;
use App\Models\Wallet;
use App\Models\Store;
use App\Models\User;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;

class WalletResource extends Resource
{
    protected static ?string $model = Wallet::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('store_id')
                    ->label('Toko')
                    ->options(Store::pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),

                Forms\Components\Select::make('user_id')
                    ->label('User (Wallet Pribadi)')
                    ->options(User::pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),

                Forms\Components\TextInput::make('balance')
                    ->label('Saldo')
                    ->numeric()
                    ->required(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('store.name')
                    ->label('Nama Toko')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama User')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('balance')
                    ->label('Saldo')
                    ->money('IDR', true)
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('store_id')
                    ->label('Toko')
                    ->options(Store::pluck('name', 'id'))
                    ->searchable(),

                Tables\Filters\SelectFilter::make('user_id')
                    ->label('User')
                    ->options(User::pluck('name', 'id'))
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWallets::route('/'),
            'create' => Pages\CreateWallet::route('/create'),
            'edit' => Pages\EditWallet::route('/{record}/edit'),
        ];
    }
}
