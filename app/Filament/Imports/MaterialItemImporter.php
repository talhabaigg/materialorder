<?php

namespace App\Filament\Imports;

use App\Models\MaterialItem;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class MaterialItemImporter extends Importer
{
    protected static ?string $model = MaterialItem::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('code')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('description'),
            ImportColumn::make('supplier_name')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('costcode')
                ->requiredMapping()
                ->rules(['required']),
                
    ];
       
    }

    public function resolveRecord(): ?MaterialItem
    {
        // Find existing record by 'code' or create a new one if it doesn't exist
    $materialItem = MaterialItem::firstOrNew([
        'code' => $this->data['code'], // Use 'code' as the unique field
    ]);

    // Format the costcode field (e.g., 21828 -> 21-828)
    if (isset($this->data['costcode'])) {
        $costcode = $this->data['costcode'];

        // Split the string after the first two digits and format as XX-XXX
        $formattedCostcode = substr($costcode, 0, 2) . '-' . substr($costcode, 2);
        $materialItem->costcode = $formattedCostcode;
    }
    
        return $materialItem;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your material item import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
