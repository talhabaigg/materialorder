<?php

namespace App\Filament\Imports;

use App\Models\ItemProjectPrice;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class ItemProjectPriceImporter extends Importer
{
    protected static ?string $model = ItemProjectPrice::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('price_list')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('item_code')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('project_number')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('price')
                ->requiredMapping()
                ->rules(['required']),
        ];
    }

    public function resolveRecord(): ?ItemProjectPrice
    {
        // return ItemProjectPrice::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new ItemProjectPrice();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your item project price import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
