<?php

namespace App\Filament\Resources\ItemBaseResource\Pages;

use App\Filament\Resources\ItemBaseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditItemBase extends EditRecord
{
    protected static string $resource = ItemBaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
