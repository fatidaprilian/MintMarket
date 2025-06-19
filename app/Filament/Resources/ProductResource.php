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

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Product Details')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state))),
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->unique(Product::class, 'slug', ignoreRecord: true)
                                    ->readOnly(),
                                Forms\Components\RichEditor::make('description')
                                    ->columnSpanFull()
                                    ->disableToolbarButtons(['attachFiles']),
                            ])->columns(2),
                        Forms\Components\Section::make('Pricing and Status')
                            ->schema([
                                Forms\Components\TextInput::make('price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('IDR'),
                                Forms\Components\Select::make('condition')
                                    ->options(['baru' => 'Baru', 'bekas' => 'Bekas Layak Pakai'])
                                    ->required(),
                                Forms\Components\Select::make('status')
                                    ->options(['tersedia' => 'Tersedia', 'terjual' => 'Terjual', 'menunggu_verifikasi' => 'Menunggu Verifikasi'])
                                    ->required()
                                    ->default('tersedia'),
                            ])->columns(3),
                    ])->columnSpan(2),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Associations')
                            ->schema([
                                Forms\Components\Select::make('store_id')
                                    ->relationship(
                                        name: 'store',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn(Builder $query) => $query->where('is_active', true)
                                    )
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->label('Toko'),
                                Forms\Components\Select::make('category_id')
                                    ->relationship('category', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->label('Category'),
                            ]),
                        Forms\Components\Section::make('Product Image')
                            ->schema([
                                Forms\Components\FileUpload::make('image')
                                    ->image()
                                    ->multiple()
                                    ->directory('product-images')
                                    ->reorderable(),
                            ]),
                    ])->columnSpan(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('image')
                    ->label('Thumbnail')
                    ->getStateUsing(function ($record) {
                        return $record->image[0] ?? null;
                    }),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('store.name')
                    ->label('Toko')
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'tersedia' => 'success',
                        'terjual' => 'danger',
                        'menunggu_verifikasi' => 'warning',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
