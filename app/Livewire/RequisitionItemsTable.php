<?php

namespace App\Livewire;

use Livewire\Component;
use Filament\Tables\Table;
use App\Models\RequisitionLineItem;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

class RequisitionItemsTable extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public $record;
    public function mount($record) // Use mount to initialize the property
    {
        $this->record = $record; // Assign the passed record to the property
    }
    public function table(Table $table): Table
    {
        return $table
            ->query(RequisitionLineItem::where('requisition_id', $this->record->id))
            ->columns([
                TextColumn::make('requisition.supplier_name')->label('Supplier'),
                TextColumn::make('item_code'),
                TextColumn::make('description'),
                
                TextColumn::make('qty'),
                TextColumn::make('cost'),
                TextColumn::make('price_list'),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                // ...
            ])
            ->bulkActions([
                // ...
            ]);
    }

    public function render()
    {
        return view('livewire.requisition-items-table');
    }
}
