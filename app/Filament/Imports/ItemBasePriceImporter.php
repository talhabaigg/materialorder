<?php

namespace App\Filament\Imports;

use App\Models\MaterialItem;
use App\Models\ItemBasePrice;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;

class ItemBasePriceImporter extends Importer
{
    protected static ?string $model = ItemBasePrice::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('item_base_id')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('material_item_code') // Change to 'material_item_code'
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('material_item_id') // Change to 'material_item_code'
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('price')
                ->requiredMapping(),
        ];
    }

    public function resolveRecord(): ?ItemBasePrice
    {
        // Retrieve and clean the material item code and ID from the imported data
        $materialItemCode = trim($this->data['material_item_code']);
        $materialItemId = trim($this->data['material_item_id']);



        // Create or update the ItemBasePrice recorddd
        return ItemBasePrice::updateOrCreate(
            [
                'item_base_id' => $materialItemId,

                'material_item_code' => $materialItemCode,
            ],
            [
                'material_item_id' => $materialItemId, // Use the retrieved material_item_id
                'price' => 2.6, // Use the determined price value
                'created_by' => Auth::id(), // Set created_by to the authenticated user ID
                'updated_by' => Auth::id(), // Set updated_by to the authenticated user ID
            ]
        );
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your item base price import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
