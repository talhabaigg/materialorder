<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\RequisitionResource;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Filament\Resources\RequisitionLineItemResource;
use App\Models\RequisitionLineItem;


class FrequentlyOrderedItems extends BaseWidget
{
    protected static ?int $sort = 3;
    public $items = [];
    public function table(Table $table): Table
    {
        return $table
        ->query(
            RequisitionLineItemResource::getEloquentQuery()
                ->select('id','item_code','description', DB::raw('SUM(qty) as total'))
                ->groupBy('item_code', 'id', 'description')
                ->orderBy('total', 'desc')
                
                
        )
    
        ->columns([
            TextColumn::make('item_code')
                ->label('Item Code'),
            TextColumn::make('description')
                ->label('Item Code'),
            TextColumn::make('total')
                ->label('Total Quantity Sold')
        ]);
        
    }
}
