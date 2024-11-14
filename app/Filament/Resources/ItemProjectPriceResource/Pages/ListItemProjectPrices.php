<?php

namespace App\Filament\Resources\ItemProjectPriceResource\Pages;

use App\Filament\Resources\ItemProjectPriceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListItemProjectPrices extends ListRecords
{
    protected static string $resource = ItemProjectPriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
