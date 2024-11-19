<?php

namespace App\Filament\Resources\RequisitionLineItemResource\Pages;

use App\Filament\Resources\RequisitionLineItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRequisitionLineItem extends EditRecord
{
    protected static string $resource = RequisitionLineItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
