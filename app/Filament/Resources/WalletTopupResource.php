<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WalletTopupResource\Pages;
use App\Models\WalletTopup;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WalletTopupResource extends Resource
{
    protected static ?string $model = WalletTopup::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Wallet Topups';
    protected static ?string $navigationGroup = 'Keuangan';

    // NONAKTIFKAN CREATE DARI ADMIN
    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Tidak perlu, admin tidak create
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('User')->sortable(),
                Tables\Columns\TextColumn::make('amount')->money('IDR')->sortable(),
                Tables\Columns\TextColumn::make('status')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn($record) => $record->status === 'pending')
                    ->action(function ($record) {
                        $record->status = 'approved';
                        $record->save();

                        // Cari wallet user
                        $wallet = Wallet::where('user_id', $record->user_id)->first();

                        if ($wallet) {
                            $wallet->balance += $record->amount;
                            $wallet->save();
                        } else {
                            // Jika wallet belum ada, buat baru
                            $wallet = Wallet::create([
                                'user_id' => $record->user_id,
                                'balance' => $record->amount,
                            ]);
                        }

                        // Catat transaksi ke wallet_transactions
                        WalletTransaction::create([
                            'wallet_id'        => $wallet->id,
                            'reference_type'   => 'topup',
                            'reference_id'     => $record->id, // id topup
                            'amount'           => $record->amount,
                            'type'             => 'credit', // top up = credit
                            'description'      => 'Top up dari admin',
                            'running_balance'  => $wallet->balance,
                        ]);
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn($record) => $record->status === 'pending')
                    ->action(function ($record) {
                        $record->status = 'rejected';
                        $record->save();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWalletTopups::route('/'),
        ];
    }
}
