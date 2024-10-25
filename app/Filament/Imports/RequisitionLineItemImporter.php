<?php

namespace App\Filament\Imports;

use App\Models\RequisitionLineItem;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class RequisitionLineItemImporter extends Importer
{
    protected static ?string $model = RequisitionLineItem::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('requisition_id')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('item_code')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('description')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('qty')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('cost')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
        ];
    }

    public function resolveRecord(): ?RequisitionLineItem
    {
        // return RequisitionLineItem::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new RequisitionLineItem();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your requisition line item import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
