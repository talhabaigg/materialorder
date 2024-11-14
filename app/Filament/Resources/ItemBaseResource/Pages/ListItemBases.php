<?php

namespace App\Filament\Resources\ItemBaseResource\Pages;

use App\Filament\Resources\ItemBaseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListItemBases extends ListRecords
{
    protected static string $resource = ItemBaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
