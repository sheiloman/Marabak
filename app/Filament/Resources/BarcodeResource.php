<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Barcode;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\BarcodeResource\Pages;

class BarcodeResource extends Resource
{
    protected static ?string $model = Barcode::class;

    protected static ?string $navigationIcon = 'heroicon-o-qr-code';

    protected static ?string $navigationLabel = 'Barcode / Qr';

    protected static ?string $label = 'QR';

    protected static ?string $pluralLabel = 'List Barcode / Qr';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('table_number')
                    ->label('No. Meja')
                    ->readOnly()
                    ->required()
                    ->maxLength(255)
                    ->default(fn() => strtoupper(chr(rand(65, 90)) . rand(1000, 9999)))
                    ->columnSpanFull(),
                Forms\Components\Select::make('users_id')
                    ->label('Pembuat Kode QR')
                    ->required()
                    ->relationship('users', 'name')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('image')
                    ->label('Gambar / Foto')
                    ->image()
                    ->directory('qr_codes')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('table_number')
                    ->label('No. Meja')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image')
                    ->label('Gambar QR')
                    ->disk('public'),
                Tables\Columns\TextColumn::make('qr_value')
                    ->label('QR Url')
                    ->searchable(),
                Tables\Columns\TextColumn::make('users.name')
                    ->label('Pembuat QR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Download Kode QR')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function ($record) {
                        $filePath = storage_path('app/public/' . $record->image);
                        if (file_exists($filePath)) {
                            return response()->download($filePath);
                        }

                        session()->flash('error', 'Gambar kode QR tidak ditemukan.');
                    }),
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
            'index' => Pages\ListBarcodes::route('/'),
            'create' => Pages\CreateQr::route('/create'),
            'edit' => Pages\EditBarcode::route('/{record}/edit'),
        ];
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }
}
