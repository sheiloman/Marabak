<?php

namespace App\Filament\Resources\TransactionItemsResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Traits\HasParentResource;
use App\Filament\Resources\TransactionItemsResource;

class ListTransactionItems extends ListRecords
{
    use HasParentResource;

    protected static string $resource = TransactionItemsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
