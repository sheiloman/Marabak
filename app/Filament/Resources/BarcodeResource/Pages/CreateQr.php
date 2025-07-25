<?php

namespace App\Filament\Resources\BarcodeResource\Pages;

use Filament\Forms;
use App\Models\Barcode;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use App\Filament\Resources\BarcodeResource;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CreateQr extends Page
{
    protected static string $resource = BarcodeResource::class;

    protected static string $view = 'filament.resources.barcode-resource.pages.create-qr';

    public $table_number;

    public function mount(): void
    {
        $this->form->fill();
        $this->table_number = strtoupper(chr(rand(65, 90)) . rand(1000, 9999));
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('table_number')
                    ->label('No. Meja')
                    ->disabled()
                    ->required()
                    ->default(fn() => $this->table_number),
            ]);
    }

    public function save(): void
    {
        $host = $_SERVER['HTTP_HOST'] . '/' . $this->table_number; // domain.com/EXP1234

        // Generate Kode QR sebagai SVG
        $svgContent = QrCode::margin(1)->size(200)->generate($host);

        // Path SVG Kode QR
        $svgFilePath = 'qr_codes/' . $this->table_number . '.svg';

        // Simpan SVG kedalam folder storage
        Storage::disk('public')->put($svgFilePath, $svgContent);

        // Simpan data ke tabel barcodes
        Barcode::create([
            'table_number'  => $this->table_number,
            'users_id'       => Auth::user()->id,
            'image'         => $svgFilePath,
            'qr_value'      => $host, // QR URl
        ]);

        // Kasih notifikasi
        Notification::make()
            ->title('Kode QR Berhasil Dibuat!')
            ->success()
            ->icon('heroicon-o-check-circle')
            ->send();

        // Balik ke halaman list barcodes
        $this->redirect(url('admin/barcodes'));
    }
}
