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
    protected static ?string $navigationLabel = 'Wallet';
    protected static ?string $navigationGroup = 'Keuangan';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('store_id')
                    ->label('Toko')
                    ->options(fn() => Store::pluck('name', 'id')->toArray())
                    ->searchable()
                    ->nullable()
                    ->placeholder('— Jika wallet toko —'),

                Forms\Components\Select::make('user_id')
                    ->label('User (Wallet Pribadi)')
                    ->options(fn() => User::pluck('name', 'id')->toArray())
                    ->searchable()
                    ->nullable()
                    ->placeholder('— Jika wallet user —'),

                Forms\Components\TextInput::make('balance')
                    ->label('Saldo')
                    ->numeric()
                    ->required()
                    ->prefix('Rp'),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('store.name')
                    ->label('Nama Toko')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama User')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('balance')
                    ->label('Saldo')
                    ->money('IDR', true)
                    ->sortable()
                    ->alignRight()
                    ->description(fn(Wallet $record) => $record->store_id ? 'Wallet Toko' : 'Wallet User'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y H:i')
                    ->label('Dibuat')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('store_id')
                    ->label('Toko')
                    ->options(fn() => Store::pluck('name', 'id')->toArray())
                    ->searchable(),
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('User')
                    ->options(fn() => User::pluck('name', 'id')->toArray())
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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
            'view' => Pages\ViewWallet::route('/{record}'),
        ];
    }
}
