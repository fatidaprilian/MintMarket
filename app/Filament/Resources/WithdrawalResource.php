<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WithdrawalResource\Pages;
use App\Models\Withdrawal;
use App\Models\Wallet;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Tables;

class WithdrawalResource extends Resource
{
    protected static ?string $model = Withdrawal::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('withdrawal_code')
                ->label('Kode Withdraw')
                ->disabled(),
            Forms\Components\TextInput::make('amount')
                ->label('Nominal')
                ->disabled(),
            Forms\Components\Select::make('status')
                ->label('Status')
                ->options([
                    'pending' => 'Pending',
                    'processing' => 'Diproses',
                    'completed' => 'Selesai',
                    'rejected' => 'Ditolak',
                ])
                ->disabled(),
            Forms\Components\TextInput::make('bank_name')
                ->label('Bank')
                ->disabled(),
            Forms\Components\TextInput::make('account_holder_name')
                ->label('Nama Pemilik Rekening')
                ->disabled(),
            Forms\Components\TextInput::make('account_number')
                ->label('No. Rekening')
                ->disabled(),
            Forms\Components\Textarea::make('rejection_reason')
                ->label('Alasan Penolakan')
                ->disabled(),
            Forms\Components\DateTimePicker::make('processed_at')
                ->label('Diproses Pada')
                ->disabled(),
            Forms\Components\TextInput::make('wallet.user.name')
                ->label('User')
                ->disabled(),
            Forms\Components\TextInput::make('wallet.store.name')
                ->label('Toko')
                ->disabled(),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('withdrawal_code')->label('Kode'),
            Tables\Columns\TextColumn::make('wallet.user.name')->label('User')->searchable(),
            Tables\Columns\TextColumn::make('wallet.store.name')->label('Toko')->searchable(),
            Tables\Columns\TextColumn::make('amount')->label('Nominal')->money('IDR', true)->sortable(),
            Tables\Columns\BadgeColumn::make('status')
                ->label('Status')
                ->colors([
                    'warning' => 'pending',
                    'info' => 'processing',
                    'success' => 'completed',
                    'danger' => 'rejected',
                ])
                ->formatStateUsing(fn($state) => match ($state) {
                    'pending' => 'Pending',
                    'processing' => 'Diproses',
                    'completed' => 'Selesai',
                    'rejected' => 'Ditolak',
                    default => ucfirst($state),
                }),
            Tables\Columns\TextColumn::make('bank_name')->label('Bank'),
            Tables\Columns\TextColumn::make('account_holder_name')->label('Nama Rek'),
            Tables\Columns\TextColumn::make('account_number')->label('No Rek'),
            Tables\Columns\TextColumn::make('processed_at')->label('Waktu Proses')->dateTime('d M Y H:i'),
            Tables\Columns\TextColumn::make('created_at')->label('Diajukan')->dateTime('d M Y H:i'),
        ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Diproses',
                        'completed' => 'Selesai',
                        'rejected' => 'Ditolak',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWithdrawals::route('/'),
            'view' => Pages\ViewWithdrawal::route('/{record}'),
        ];
    }
}
