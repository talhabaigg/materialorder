<?php

namespace App\Filament\Resources\RequisitionResource\Widgets;

use App\Models\Requisition;
use Illuminate\Support\Number;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '3s';
    protected function getStats(): array
    {
        $pending = Requisition::where('is_processed', false)->count();
        $requisitions = Requisition::where('is_processed', true)->count();

        return [
            // Stat::make('Pending Requisitions', Number::format($pending))
            //     ->icon('heroicon-o-question-mark-circle'),
            // Stat::make('Completed Requisitions', Number::format($requisitions))
            //     ->icon('heroicon-o-check-circle'),
        ];
    }
}
