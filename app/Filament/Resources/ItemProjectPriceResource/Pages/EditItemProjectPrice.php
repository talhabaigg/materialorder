<?php

namespace App\Filament\Resources\ItemProjectPriceResource\Pages;

use App\Filament\Resources\ItemProjectPriceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditItemProjectPrice extends EditRecord
{
    protected static string $resource = ItemProjectPriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
