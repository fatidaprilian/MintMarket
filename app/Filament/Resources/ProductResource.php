<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Set;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    public static function form(Form $form): Form
    {
        // --- Logika untuk menentukan sesi Flash Sale berikutnya ---
        $schedule = [9, 22]; // Jam mulai sesi (10:00 dan 20:00 WIB)
        $now = now();
        $nextSessionStart = null;

        foreach ($schedule as $hour) {
            $potentialSessionStart = $now->copy()->setTime($hour, 0, 0);
            if ($potentialSessionStart->isFuture()) {
                $nextSessionStart = $potentialSessionStart;
                break;
            }
        }

        if (!$nextSessionStart) {
            $nextSessionStart = $now->copy()->addDay()->setTime($schedule[0], 0, 0);
        }

        $nextSessionEnd = $nextSessionStart->copy()->addHours(14);
        $formattedEndTimeForValue = $nextSessionEnd->format('Y-m-d H:i:s');
        $sessionLabelForOption = 'Sesi ' . $nextSessionStart->format('d M, H:i');
        $sessionDescription = 'Produk akan tayang pada sesi Flash Sale berikutnya yang dimulai pada: ' . $nextSessionStart->format('d M Y, H:i') . ' WIB.';
        // --- Akhir Logika ---

        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Product Details')
                            ->schema([
                                Forms\Components\TextInput::make('name')->required()->live(onBlur: true)->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state))),
                                Forms\Components\TextInput::make('slug')->required()->unique(Product::class, 'slug', ignoreRecord: true)->readOnly(),
                                Forms\Components\RichEditor::make('description')->columnSpanFull()->disableToolbarButtons(['attachFiles']),
                            ])->columns(2),

                        Forms\Components\Section::make('Pricing and Status')
                            ->schema([
                                Forms\Components\TextInput::make('price')->required()->numeric()->prefix('IDR'),
                                Forms\Components\TextInput::make('original_price')->label('Harga Asli (dicoret)')->numeric()->prefix('IDR'),
                                Forms\Components\TextInput::make('stock')->required()->numeric()->default(0),
                                Forms\Components\Select::make('condition')->options(['baru' => 'Baru', 'bekas' => 'Bekas Layak Pakai'])->required(),
                                Forms\Components\Select::make('status')->options(['tersedia' => 'Tersedia', 'terjual' => 'Terjual', 'menunggu_verifikasi' => 'Menunggu Verifikasi'])->required()->default('tersedia'),
                            ])->columns(3),

                        Forms\Components\Section::make('Flash Sale (Opsional)')
                            ->description($sessionDescription)
                            ->schema([
                                Forms\Components\TextInput::make('flash_sale_price')
                                    ->label('Harga Flash Sale')
                                    ->numeric()->prefix('IDR')
                                    ->requiredWith('flash_sale_end_date'),

                                Forms\Components\Select::make('flash_sale_end_date')
                                    ->label('Pilih Sesi Flash Sale')
                                    ->options([
                                        $formattedEndTimeForValue => $sessionLabelForOption
                                    ])
                                    ->requiredWith('flash_sale_price')
                                    ->helperText('Hanya sesi berikutnya yang tersedia untuk pendaftaran.'),
                            ])->columns(2),

                    ])->columnSpan(2),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Associations')
                            ->schema([
                                Forms\Components\Select::make('store_id')->relationship(name: 'store', titleAttribute: 'name', modifyQueryUsing: fn(Builder $query) => $query->where('is_active', true))->required()->searchable()->preload()->label('Toko'),
                                Forms\Components\Select::make('category_id')->relationship('category', 'name')->required()->searchable()->preload()->label('Category'),
                            ]),
                        Forms\Components\Section::make('Product Image')
                            ->schema([
                                Forms\Components\FileUpload::make('image')->image()->multiple()->directory('product-images')->reorderable(),
                            ]),
                    ])->columnSpan(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\ImageColumn::make('image')->label('Thumbnail')->getStateUsing(fn($record) => $record->image[0] ?? null),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('store.name')->label('Toko')->sortable(),
                Tables\Columns\TextColumn::make('price')->label('Harga Normal')->money('IDR')->sortable(),
                Tables\Columns\TextColumn::make('stock')->label('Stok')->sortable(),
                Tables\Columns\TextColumn::make('flash_sale_price')->label('Harga Flash Sale')->money('IDR')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('flash_sale_end_date')->label('Akhir Flash Sale')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'tersedia' => 'success',
                        'terjual' => 'danger',
                        'menunggu_verifikasi' => 'warning',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('Verify')
                    ->action(function (Product $record) {
                        $record->status = 'tersedia';
                        $record->save();
                    })
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn(Product $record): bool => $record->status === 'menunggu_verifikasi'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
