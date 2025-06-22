<?php

namespace App\Filament\Resources\WithdrawalResource\Pages;

use App\Filament\Resources\WithdrawalResource;
use App\Models\Withdrawal;
use App\Models\WalletTransaction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class ListWithdrawals extends ListRecords
{
    protected static string $resource = WithdrawalResource::class;

    protected function getTableActions(): array
    {
        return [
            Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn($record) => $record->status === 'pending')
                ->action(function (Withdrawal $record) {
                    DB::transaction(function () use ($record) {
                        // Update status withdrawal
                        $record->update([
                            'status' => 'completed',
                            'processed_at' => now(),
                            'rejection_reason' => null,
                        ]);

                        // Kurangi saldo wallet & catat mutasi
                        $wallet = $record->wallet;
                        if ($wallet->balance < $record->amount) {
                            throw new \Exception('Saldo wallet tidak cukup');
                        }
                        $wallet->balance -= $record->amount;
                        $wallet->save();

                        $wallet->transactions()->create([
                            'amount' => $record->amount,
                            'type' => 'withdraw',
                            'description' => 'Penarikan saldo #' . $record->withdrawal_code,
                            'running_balance' => $wallet->balance,
                            'reference_type' => Withdrawal::class,
                            'reference_id' => $record->id,
                        ]);
                    });

                    Notification::make()
                        ->title('Withdrawal disetujui & saldo terdebit')
                        ->success()
                        ->send();
                }),

            Action::make('reject')
                ->label('Reject')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn($record) => $record->status === 'pending')
                ->form([
                    \Filament\Forms\Components\Textarea::make('rejection_reason')
                        ->label('Alasan Penolakan')
                        ->required(),
                ])
                ->action(function (Withdrawal $record, array $data) {
                    $record->update([
                        'status' => 'rejected',
                        'rejection_reason' => $data['rejection_reason'],
                        'processed_at' => now(),
                    ]);
                    Notification::make()
                        ->title('Withdrawal ditolak')
                        ->danger()
                        ->send();
                })
        ];
    }
}
