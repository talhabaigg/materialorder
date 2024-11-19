<?php

namespace App\Filament\Resources\RequisitionLineItemResource\Pages;

use App\Filament\Resources\RequisitionLineItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRequisitionLineItems extends ListRecords
{
    protected static string $resource = RequisitionLineItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
