<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StoreResource\Pages;
use App\Models\Store;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StoreResource extends Resource
{
    protected static ?string $model = Store::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Toko')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required(),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(Store::class, 'slug', ignoreRecord: true),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->label('Owner'),
                        Forms\Components\Toggle::make('is_active')
                            ->required()
                            ->label('Toko Aktif?'),
                        Forms\Components\RichEditor::make('description')
                            ->columnSpanFull(),
                    ])->columns(2),

                // Section baru untuk lokasi
                Forms\Components\Section::make('Lokasi Toko')
                    ->schema([
                        Forms\Components\TextInput::make('province')
                            ->label('Provinsi'),
                        Forms\Components\TextInput::make('city')
                            ->label('Kota/Kabupaten'),
                        Forms\Components\Textarea::make('address')
                            ->label('Alamat Lengkap')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Owner')
                    ->searchable(),
                // Tambahkan kolom lokasi di tabel
                Tables\Columns\TextColumn::make('city')
                    ->label('Kota')
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Details'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    // ... sisa file (getRelations, getPages) biarkan seperti semula ...
    public static function getRelations(): array
    {
        return [];
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStores::route('/'),
            'edit' => Pages\EditStore::route('/{record}/edit'),
        ];
    }
}
