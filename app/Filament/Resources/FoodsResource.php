<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Foods;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use App\Filament\Resources\FoodsResource\Pages;

class FoodsResource extends Resource
{
    protected static ?string $model = Foods::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Menu Makanan'; // <- ini untuk sidebar
    protected static ?string $label = 'Menu';
    protected static ?string $pluralLabel = 'List Menu';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make()
                    ->tabs([
                        Tab::make('Informasi Menu')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama')
                                    ->required()
                                    ->columnSpanFull(),
                                Forms\Components\RichEditor::make('description')
                                    ->label('Deskripsi')
                                    ->required()
                                    ->columnSpanFull(),
                                Forms\Components\FileUpload::make('image')
                                    ->label('Gambar')
                                    ->image()
                                    ->directory('foods')
                                    ->required()
                                    ->columnSpanFull(),
                                Forms\Components\Select::make('categories_id')
                                    ->label('Kategori')
                                    ->required()
                                    ->relationship('categories', 'name')
                                    ->columnSpanFull(),
                            ]),

                        Tab::make('Harga & Diskon')
                            ->schema([
                                Forms\Components\TextInput::make('price')
                                    ->label('Harga')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->reactive()
                                    ->columnSpanFull(),
                                Forms\Components\Toggle::make('is_promo')
                                    ->label('Apa ada promo ?')
                                    ->reactive(),
                                Forms\Components\Select::make('percent')
                                    ->label('Diskon')
                                    ->options([
                                        10 => '10%',
                                        25 => '25%',
                                        30 => '30%',
                                        50 => '50%',
                                    ])
                                    ->columnSpanFull()
                                    ->reactive()
                                    ->hidden(fn($get) => !$get('is_promo'))
                                    ->afterStateUpdated(function ($set, $get, $state) {
                                        if ($get('is_promo') && $get('price') && $get('percent')) {
                                            $discount = ($get('price') * (int)$get('percent')) / 100;
                                            $set('price_afterdiscount', $get('price') - $discount);
                                        } else {
                                            $set('price_afterdiscount', $get('price'));
                                        }
                                    }),
                                Forms\Components\TextInput::make('price_afterdiscount')
                                    ->label('Harga Setelah Diskon')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->readOnly()
                                    ->columnSpanFull()
                                    ->hidden(fn($get) => !$get('is_promo')),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Makanan')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image')
                    ->label('Gambar')
                    ->disk('public'),
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR')
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('percent')
                    ->label('Diskon (%)')
                    ->getStateUsing(fn ($record) => $record->is_promo ? $record->percent : null)
                    ->formatStateUsing(fn ($state) => !empty($state) ? "$state %" : '-')
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rupiah')
                    ->label('Diskon (Rp)')
                    ->getStateUsing(fn ($record) => $record->is_promo ? ($record->price * $record->percent) / 100 : null)
                    ->formatStateUsing(fn ($state) => !empty($state) ? $state : '-')
                    ->money('IDR')
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_afterdiscount')
                    ->label('Setelah Diskon')
                    ->getStateUsing(fn ($record) => $record->is_promo ? $record->price_afterdiscount : null)
                    ->formatStateUsing(fn ($state) => !empty($state) ? $state : '-')
                    ->money('IDR')
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_promo')
                    ->label('Ada Promo ?')
                    ->icon(fn (bool $state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn (bool $state) => $state ? 'success' : 'danger')
                    ->tooltip(fn (bool $state) => $state ? 'Promo Aktif' : 'Tidak Promo')
                    ->alignCenter()
                    ->boolean(),
                Tables\Columns\TextColumn::make('categories.name')
                    ->label('Kategori')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault : true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Data Diupdate')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFoods::route('/'),
            'create' => Pages\CreateFoods::route('/create'),
            'edit' => Pages\EditFoods::route('/{record}/edit'),
        ];
    }
}
