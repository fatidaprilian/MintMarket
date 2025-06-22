<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WalletTransactionResource\Pages;
use App\Models\WalletTransaction;
use App\Models\Wallet;
use Filament\Resources\Resource;
use Filament\Tables;

class WalletTransactionResource extends Resource
{
    protected static ?string $model = WalletTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    // Tidak menyediakan form, hanya readonly

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('wallet.store.name')
                    ->label('Nama Toko')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('wallet.user.name')
                    ->label('Nama User')
                    ->toggleable(),

                Tables\Columns\BadgeColumn::make('type')
                    ->label('Tipe')
                    ->colors([
                        'success' => 'credit',
                        'danger' => 'debit',
                    ])
                    ->formatStateUsing(fn($state) => $state === 'credit' ? 'Kredit' : 'Debit'),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR', true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('running_balance')
                    ->label('Saldo Setelah')
                    ->money('IDR', true)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('wallet_id')
                    ->label('Wallet')
                    ->options(
                        Wallet::with(['store', 'user'])
                            ->get()
                            ->mapWithKeys(function ($wallet) {
                                $label = '';
                                if ($wallet->store) {
                                    $label = 'Toko: ' . $wallet->store->name;
                                } elseif ($wallet->user) {
                                    $label = 'User: ' . $wallet->user->name;
                                } else {
                                    $label = 'Wallet ID: ' . $wallet->id;
                                }
                                return [$wallet->id => $label];
                            })->toArray()
                    )
                    ->searchable(),

                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipe')
                    ->options([
                        'credit' => 'Kredit',
                        'debit'  => 'Debit',
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
            // Hanya halaman list, tanpa create/edit/delete
            'index' => Pages\ListWalletTransactions::route('/'),
        ];
    }

    // Supaya tombol create (New Wallet Transaction) tidak muncul
    public static function canCreate(): bool
    {
        return false;
    }
}
