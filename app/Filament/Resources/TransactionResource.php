<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use App\Models\TransactionItem; // Tambahkan ini jika Anda belum
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder; // Tambahkan ini

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered', // Pastikan ini ada di enum migrasi transactions
                        'cancelled' => 'Cancelled',
                    ])
                    ->required()
                    ->columnSpanFull(), // Agar mengambil lebar penuh

                // Fields ini dibuat disabled karena ini detail transaksi, bukan untuk diedit langsung di form ini
                Forms\Components\TextInput::make('transaction_code')->disabled(),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name') // Mengambil nama user dari relasi 'user'
                    ->disabled()
                    ->label('Pembeli'),
                Forms\Components\Select::make('store_id')
                    ->relationship('store', 'name') // Mengambil nama toko dari relasi 'store'
                    ->disabled()
                    ->label('Toko Penjual'),
                Forms\Components\TextInput::make('total_amount')
                    ->numeric()
                    ->prefix('IDR')
                    ->disabled(),
                Forms\Components\TextInput::make('shipping_cost')
                    ->numeric()
                    ->prefix('IDR')
                    ->disabled(),
                Forms\Components\Textarea::make('shipping_address')
                    ->rows(3)
                    ->disabled(),
                Forms\Components\TextInput::make('payment_method')
                    ->disabled(),
                Forms\Components\TextInput::make('shipping_method')
                    ->disabled(),

                // Menampilkan daftar produk dalam transaksi (jika diperlukan di form edit)
                Forms\Components\Repeater::make('items')
                    ->relationship('items') // Pastikan relasi 'items' ada di model Transaction
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->relationship('product', 'name')
                            ->disabled()
                            ->label('Produk'),
                        Forms\Components\TextInput::make('quantity')
                            ->numeric()
                            ->disabled(),
                        Forms\Components\TextInput::make('price')
                            ->numeric()
                            ->prefix('IDR')
                            ->disabled(),
                    ])
                    ->columns(3) // Tampilkan 3 kolom per item
                    ->disabled() // Tidak bisa diedit di sini
                    ->columnSpanFull()
                    ->addable(false) // Tidak bisa menambah item baru dari form ini
                    ->deletable(false) // Tidak bisa menghapus item dari form ini
                    ->label('Detail Produk Transaksi'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaction_code')
                    ->label('Transaction Code')
                    ->searchable()
                    ->sortable(),

                // Menampilkan nama produk dari transaction_items
                Tables\Columns\TextColumn::make('items.product.name')
                    ->label('Produk')
                    ->listWithLineBreaks() // Menampilkan setiap nama produk di baris baru
                    ->limitList(2) // Membatasi tampilan hanya 2 produk pertama
                    ->expandableLimitedList() // Memungkinkan user untuk melihat semua produk jika lebih dari limit
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        // Custom search untuk mencari produk dalam transaction_items
                        return $query->whereHas('items.product', function (Builder $q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                    }),
                // Pengurutan untuk kolom produk (opsional, bisa rumit untuk multiple items)
                // ->sortable(query: function (Builder $query, string $direction): Builder {
                //     return $query->orderBy(TransactionItem::select('product_id')
                //         ->whereColumn('transaction_items.transaction_id', 'transactions.id')
                //         ->orderBy('id') // Urutkan berdasarkan ID item pertama sebagai contoh
                //         ->limit(1), $direction);
                // }),

                // Kolom untuk Pembeli
                Tables\Columns\TextColumn::make('user.name') // Akses melalui relasi user() di model Transaction
                    ->label('Pembeli')
                    ->searchable()
                    ->sortable(),

                // Kolom untuk Penjual
                Tables\Columns\TextColumn::make('store.user.name') // Akses melalui relasi store() lalu relasi user() dari model Store
                    ->label('Penjual')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'info',
                        'processing' => 'primary', // Tambahkan ini jika ada di enum migrasi
                        'shipped' => 'primary',    // Sesuaikan warna jika perlu
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
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
        return [
            // Jika Anda ingin ada tab terpisah di halaman edit untuk melihat detail item transaksi:
            // Relations\TransactionItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'), // Tambahkan jika ada halaman create
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
