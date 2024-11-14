<?php

namespace App\Filament\Resources\ItemBasePriceResource\Pages;

use App\Filament\Resources\ItemBasePriceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListItemBasePrices extends ListRecords
{
    protected static string $resource = ItemBasePriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
