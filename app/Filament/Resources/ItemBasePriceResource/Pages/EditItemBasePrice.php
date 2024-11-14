<?php

namespace App\Filament\Resources\ItemBasePriceResource\Pages;

use App\Filament\Resources\ItemBasePriceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditItemBasePrice extends EditRecord
{
    protected static string $resource = ItemBasePriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
