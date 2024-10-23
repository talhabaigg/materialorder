<?php

namespace App\Filament\Resources\RequisitionResource\Widgets;

use App\Models\Requisition;
use Illuminate\Support\Number;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $pending = Requisition::where('is_processed', false)->count();
        $requisitions = Requisition::where('is_processed', true)->count();;
        
        return [
            Stat::make('Pending Requisitions', Number::format($pending))
                ->description('The total number of Requisitions')
                ->icon('heroicon-o-shopping-cart'),
            Stat::make('Completed Requisitions', Number::format($requisitions))
                ->description('The total number of Requisitions')
                ->icon('heroicon-o-shopping-cart'),

           
        ];
    }
}
