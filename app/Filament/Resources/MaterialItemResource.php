<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\ItemBase;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;

use App\Models\MaterialItem;
use App\Models\ItemBasePrice;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Log;
use App\Imports\MaterialItemsImport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\CheckboxColumn;
use App\Filament\Exports\MaterialItemExporter;
use App\Filament\Imports\MaterialItemImporter;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\MaterialItemResource\Pages;
use App\Filament\Resources\MaterialItemResource\RelationManagers;

class MaterialItemResource extends Resource
{
    protected static ?string $model = MaterialItem::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationGroup = 'Admin';
    protected static ?string $navigationLabel = 'Manage Material Items';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('code'),
                TextInput::make('description'),
                TextInput::make('supplier_name'),
                TextInput::make('costcode'),
            ]);
    }

    public static function table(Table $table): Table
    {
        $activeBaseId = 1;
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Item code')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Description')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('vendor.name')
                    ->label('Supplier')
                    ->sortable()->getStateUsing(function ($record) {
                        // Access the related vendor
                        $vendor = $record->vendor; // Assuming $record is an instance of MaterialItem
                        
                        // Check if the vendor exists and return the name or supplier_name
                        return $vendor ? $vendor->name : $record->supplier_name; 
                    }),
                TextColumn::make('costcode')
                    ->label('Cost Code')
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        $costcode = (string) $record->costcode; // Ensure it's a string
                        // Split the string at the 2nd index and join with a hyphen
                        $formattedCostcode = substr($costcode, 0, 2) . '-' . substr($costcode, 2);
                        return $formattedCostcode;
                    }),
                
                
                    TextColumn::make('price')
                    ->label('Active Base Price')
                    ->sortable()
                    ->money()
                    ->getStateUsing(function ($record) {
                        $currentDate = now();
                        $itemBase = ItemBase::whereDate('effective_from', '<=', $currentDate)
                                ->whereDate('effective_to', '>=', $currentDate)->orWhereNull('effective_to')
                                ->first();
                            // dd($itemBase->id);
                            $activeBaseId = $itemBase ? $itemBase->id : null;
                        
                        $itemBasePrice = ItemBasePrice::where('material_item_code', $record->code)->where('item_base_id', $activeBaseId)->first();
        
                        return $itemBasePrice ? $itemBasePrice->price : 'No active base price set';
                    }),
                
                  // Dynamic color
                
            ])
            ->filters([
                SelectFilter::make('supplier_name')
                    ->multiple()
                    ->options(function () {
                        return MaterialItem::select('supplier_name')
                            ->distinct() // Get unique supplier names
                            ->pluck('supplier_name', 'supplier_name') // Create key-value pairs
                            ->toArray(); // Convert to array
                    }),
                    Filter::make('price_range')
                ->form([
                    TextInput::make('min_price')
                        ->label('Min Price')
                        ->numeric(),
                    TextInput::make('max_price')
                        ->label('Max Price')
                        ->numeric(),
                ])
                ->query(function (Builder $query, array $data) {
                    // Filter based on the basic relationship price
                    if (isset($data['min_price']) && $data['min_price'] !== '') {
                        $query->whereHas('basic', function (Builder $query) use ($data) {
                            $query->where('price', '>=', $data['min_price']);
                        });
                    }
                    if (isset($data['max_price']) && $data['max_price'] !== '') {
                        $query->whereHas('basic', function (Builder $query) use ($data) {
                            $query->where('price', '<=', $data['max_price']);
                        });
                    }
                })
            ])
            ->actions([
                
                Action::make('markProcessed')
                ->tooltip(fn (MaterialItem $record): string => $record->is_favourite ? 'Remove from Favourite' : 'Add to Favourite')
                ->icon('heroicon-s-star')
                ->iconButton()
                ->action(function (MaterialItem $record): void {
                    $record->is_favourite = !$record->is_favourite;  // Toggle the value
                    $record->save();  // Save the updated record
                })
                ->color(fn (MaterialItem $record): string => $record->is_favourite ? 'warning' : 'gray'),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(MaterialItemImporter::class)->label('Import'),
                ExportAction::make()
                    ->exporter(MaterialItemExporter::class)->label('Export'),
                ]);
            
                  
                    
                

    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMaterialItems::route('/'),
            'create' => Pages\CreateMaterialItem::route('/create'),
            'edit' => Pages\EditMaterialItem::route('/{record}/edit'),
        ];
    }
}
