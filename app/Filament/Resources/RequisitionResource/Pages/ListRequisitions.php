<?php

namespace App\Filament\Resources\RequisitionResource\Pages;

use Filament\Actions;
use App\Models\Requisition;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\RequisitionResource;
use App\Filament\Resources\RequisitionResource\Widgets;

class ListRequisitions extends ListRecords
{
    protected static string $resource = RequisitionResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
    // protected function getHeaderWidgets(): array
    // {
    //     return [Widgets\StatsOverview::class];
    // }
}
