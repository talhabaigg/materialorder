<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\Project;
use Filament\Widgets\ChartWidget;
use App\Models\RequisitionLineItem;

class TotalSpentByProjectsChart extends ChartWidget
{
    protected int | string | array $columnSpan = [
        'sm' => 1,
        '8xl' => 3,
    ];
    protected static ?string $heading = 'Material Ordered by Project in $';
    protected static ?int $sort = 1;
    protected function getData(): array
    { 
        $startOfRange = now()->subMonths(3)->startOfMonth();
        $endOfRange = now()->endOfMonth();

        // Get unique months (labels) in the specified range
        $months = collect();
        for ($i = 0; $i < 3; $i++) {
            $months->push(now()->subMonths($i)->format('M Y'));
        }
        $months = $months->reverse()->values();

        // Initialize the datasets
        $datasets = [
            'labels' => $months->toArray(),
            'datasets' => [],
        ];
        $projects = Project::with(['requisitions.lineItems' => function ($query) use ($startOfRange, $endOfRange) {
            $query->whereBetween('created_at', [$startOfRange, $endOfRange]);
        }])->get();
        foreach ($projects as $project) {
            $data = [];
    
            foreach ($months as $month) {
                // Sum the costs for the current project and month
                $monthlyTotal = $project->requisitions
                    ->flatMap->lineItems
                    ->filter(function ($lineItem) use ($month) {
                        return $lineItem->created_at->format('M Y') === $month;
                    })
                    ->sum(function ($lineItem) {
                        return $lineItem->qty * $lineItem->cost;
                    });
    
                $data[] = $monthlyTotal;
                
            }
    
            $datasets['datasets'][] = [
                'label' => $project->name,
                'data' => $data,
                
            ];
        }
     
        return $datasets;
        
       
    }

    protected function getType(): string
    {
        return 'line';
    }
    // private function getRandomColor(): string
    // {
    //     return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    // }
}
