<?php

namespace App\Filament\Resources\MaterialItemResource\Pages;

use App\Filament\Resources\MaterialItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMaterialItems extends ListRecords
{
    protected static string $resource = MaterialItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
