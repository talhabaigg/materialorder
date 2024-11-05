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
                    ->sortable(),
                TextColumn::make('basic.price')
                    ->label('Base Price')
                    ->sortable()
                    ->money(),
                 TextColumn::make('basic.base.name')
                    ->label('basic base')
                    ->sortable(),
                    TextColumn::make('test')
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
                Action::make('update_price')->tooltip('Update base price')->icon('heroicon-o-arrow-up-tray')->iconButton()
                    ->form([
                        FileUpload::make('upload_csv')
                            ->required()
                            ->acceptedFileTypes(['text/csv'])
                            ->label('Csv file must have the headers "item_code", "description", "qty", "cost" - Excel is not supported. Uploading items will replace any existing items - proceed with caution.')
                    ])
                    ->action(function (array $data): void {
                        set_time_limit(0);
                        if (isset($data['upload_csv']) && !empty($data['upload_csv'])) {
                            $fileName = $data['upload_csv'];
                            $path = storage_path("app/public/{$fileName}");
                
                            if (file_exists($path)) {
                                $csvData = array_map('str_getcsv', file($path));
                                $currentDate = now()->startOfDay();
                                $basePriceList = ItemBase::where('effective_from', '<=', $currentDate)
                                    ->where(function ($query) use ($currentDate) {
                                        $query->where('effective_to', '>=', $currentDate)
                                              ->orWhereNull('effective_to'); // For prices with no end date
                                    })
                                    ->first();
            
                                if (!$basePriceList) {
                                    Log::warning('No active base price list found for the current date.');
                                    return;
                                }
            
                                $baseId = $basePriceList->id;
                               
                                
                                $insertData = []; // Store data for bulk insert
            
                                foreach ($csvData as $index => $row) {
                                    // Skip header row (if present) or empty rows
                                    if ($index === 0 || count($row) < 3) {
                                        continue;
                                    }
            
                                    $itemCode = Str::trim($row[0]); // Remove leading and trailing spaces
                                   
                                    $priceFromCsv = (float) $row[2]; // Adjust based on your CSV structure
            
                                    // $materialItem = MaterialItem::where('code', $itemCode)->first();
                                    $materialItem = MaterialItem::Where('code', $itemCode)->first();

            
                                    if ($materialItem && $priceFromCsv > 0) {
                                        // Prepare for bulk insert
                                        $insertData[] = [
                                            'item_base_id' => $baseId,
                                            'material_item_id' => $materialItem->id,
                                            'price' => $priceFromCsv,
                                        ];
                                    } else {
                                        Log::warning("Material item not found or invalid price for item code: {$itemCode}");
                                    }
                                }
            
                                if (!empty($insertData)) {
                                    foreach ($insertData as $data) {
                                        ItemBasePrice::updateOrCreate(
                                            [
                                                'item_base_id' => $data['item_base_id'],
                                                'material_item_id' => $data['material_item_id'],
                                            ],
                                            [
                                                'price' => $data['price'],
                                            ]
                                        );
                                    }
                                    Log::info('Base prices updated successfully.');
                                } else {
                                    Log::warning('No valid base prices to update.');
                                }
                            } else {
                                Log::warning("CSV file does not exist at the specified path: {$path}");
                            }
                        }
                    })
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
