<?php

namespace App\Filament\Resources\MaterialItemResource\Pages;

use App\Filament\Resources\MaterialItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMaterialItem extends EditRecord
{
    protected static string $resource = MaterialItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
