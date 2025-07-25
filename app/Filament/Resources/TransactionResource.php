<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;

use Filament\Tables\Table;
use App\Models\Transaction;
use App\Models\TransactionItems;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Support\Htmlable;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionItemsResource\Pages\EditTransactionItems;
use App\Filament\Resources\TransactionItemsResource\Pages\ListTransactionItems;
use App\Filament\Resources\TransactionItemsResource\Pages\CreateTransactionItems;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationLabel = 'Transaksi';

    protected static ?string $label = 'Transaksi';

    protected static ?string $pluralLabel = 'List Transaksi';

    public static function getRecordTitle(?Model $record): string|null|Htmlable
    {
        return $record->name;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('external_id')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('checkout_link')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('barcodes_id')
                    ->label('QR Code')
                    ->image() // Hanya menerima file gambar
                    ->directory('qr_code') // Direktori penyimpanan
                    ->disk('public') // Disk penyimpanan
                    ->default(function ($record) {
                        return $record->barcodes->image ?? null;
                    }),
                Forms\Components\TextInput::make('payment_method')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('payment_status')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('subtotal')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('ppn')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('total')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode Trx')
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('barcodes.image')
                    ->label('Barcode')
                    ->alignCenter()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Trx')
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('No. HP')
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('subtotal')
                    ->label('Sub Total')
                    ->alignCenter()
                    ->numeric()
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ppn')
                    ->label('PPN (Rp)')
                    ->alignCenter()
                    ->numeric()
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->alignCenter()
                    ->numeric()
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Status')
                    ->alignCenter()
                    ->colors([
                        'success' => fn ($state): bool => in_array($state, ['SUCCESS', 'PAID', 'SETTLED']),
                        'warning' => fn ($state): bool => $state == 'PENDING',
                        'danger' => fn ($state): bool => in_array($state, ['FAILED', 'EXPIRED']),
                    ])
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->alignCenter()
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->alignCenter()
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('Lihat Transaksi')
                    ->color('success')
                    ->url(
                        fn (Transaction $trx): string => static::getUrl('transaction-items.index', [
                            'trxId' => $trx->id
                        ])
                    )
            ])
            ->bulkActions([]);
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
            'transaction-items.index' => ListTransactionItems::route('/{trxId}/transaction')
        ];
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }
}
