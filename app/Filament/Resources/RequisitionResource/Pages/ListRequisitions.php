<?php

namespace App\Filament\Resources\RequisitionResource\Pages;

use Filament\Actions;
use App\Filament\Resources\RequisitionResource\Widgets;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\RequisitionResource;

class ListRequisitions extends ListRecords
{
    protected static string $resource = RequisitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
            Widgets\StatsOverview::class,
        ];
    }
}
