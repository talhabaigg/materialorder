<?php

namespace App\Filament\Resources;

use Closure;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Project;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Requisition;
use App\Models\MaterialItem;
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ImportAction;
use Illuminate\Support\HtmlString;
use App\Models\RequisitionLineItem;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\ActionSize;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Navigation\NavigationItem;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Actions\ReplicateAction;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RequisitionResource\Pages;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;
use App\Filament\Resources\RequisitionResource\RelationManagers;
use App\Filament\Resources\RequisitionResource\Widgets\StatsOverview;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Tapp\FilamentGoogleAutocomplete\Forms\Components\GoogleAutocomplete;


class RequisitionResource extends Resource implements HasShieldPermissions
{
    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
            'process',
            'unprocess',
            'upload'
        ];
    }
    protected static ?string $model = Requisition::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    protected static ?string $navigationGroup = 'Collections';
    
    protected static ?int $navigationSort = 1;
    public static function getNavigationBadge(): ?string
    {
        return number_format(static::getModel()::where('is_processed', false)->count());
    }
    public static function canEdit($record): bool
    {
        return !$record->is_processed; // Disable editing if is_processed is true
    }
    public static function getWidgets(): array
{
    return [
        RequisitionResource\Widgets\StatsOverview::class,
    ];
    

}
    
    public static function form(Form $form): Form
    {
        return $form
        
        
        ->schema([
           
           

       
            Forms\Components\Card::make('Delivery details')
           
                ->schema([
                    
                    Grid::make(2) // Organize inputs in 3 columns
                        ->schema([
                            
                            DatePicker::make('date_required')
                                ->label('Date Required')
                                ->required()
                                
                                ->columnSpan(1)
                                ->minDate(now()->addDay(0)),
                            TimePicker::make('pickup_time')->default('10:00')->withoutSeconds()->label('Delivery Time'),
                           
                            Select::make('project_id')
                                ->label('Project')
                                ->options(
                                    Project::query()
                                        ->pluck('name', 'id') // Correct order: 'name' as the value, 'id' as the key
                                        ->toArray()
                                )->searchable()
                                ->reactive()
                                ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                    // Find the selected project by ID
                                    $project = Project::find($state);
                            
                                    // If a project is found, set the 'deliver_to' and 'delivery_contact' fields
                                    if ($project) {
                                        
                                        $set('deliver_to', $project->deliver_to); // Set the 'deliver_to' field
                                        $set('delivery_contact', $project->delivery_contact); // Set the 'delivery_contact' field
                                        $set('coordinates', $project->coordinates); 
                                        $set('site_reference', $project->site_reference); 
                                        $set('pickup_by', $project->pickup_by); 
                                        $set('requested_by', $project->requested_by); 
                                        $set('notes', $project->notes); 
                                    
                                    } else {
                                        // If no project is selected, clear the fields (optional)
                                        $set('deliver_to', null);
                                        $set('delivery_contact', null);
                                    }
                                }),
                            // Select::make('project_id')
                            //     ->label('Project')
                            //     ->required()
                            //     ->columnSpan(1),

                                Select::make('supplier_id')
                                ->label('Supplier')
                                ->required()
                                ->columnSpan(1)
                                ->options(
                                    MaterialItem::query()
                                        ->select('supplier_name')
                                        ->distinct()
                                        ->pluck('supplier_name', 'supplier_name')
                                        ->toArray()
                                )
                                ->searchable() // Add searchable to enhance UX,
                                ->reactive()
                                ->afterStateUpdated(function (callable $set) {
                                   $set('lineItems.*.item_code', null); // Clear item_code in line items if needed
                                   $set('lineItems.*.description', null); // Clear description in line items if needed
                                }),

                        ]),
                    
                    Grid::make(2) // 2-column layout for the next fields
                        ->schema([
                            TextInput::make('site_reference')
                                ->label('Site Reference')
                                ->required()
                                ->columnSpan(1),
                                
                            TextInput::make('delivery_contact')
                                ->label('Delivery Contact')
                                ->required()
                                ->columnSpan(1),
                        ]),
                    
                    Grid::make(2)
                        ->schema([
                            TextInput::make('pickup_by')
                                ->label('Pickup By')
                                ->required()
                                ->columnSpan(1),
                                TextInput::make('requested_by'),  
                            // Select::make('requested_by')
                            //     ->label('Requested By')
                            //     ->required()
                            //     ->columnSpan(1),
                        ]),
                    
                    // TextInput::make('deliver_to')
                    //     ->label('Deliver To')
                    //     ->required()
                    //     ->columnSpanFull()
                    //     ,
                        GoogleAutocomplete::make('google_search')
                        ->label('Google look-up')
                        ->countries([
                            'US',
                            'AU',
                        ])
                        ->language('pt-BR')
                        ->withFields([
                            Forms\Components\TextInput::make('deliver_to')
                                ->extraInputAttributes([
                                    'data-google-field' => '{street_number} {route}, {sublocality_level_1}, {locality}, {administrative_area_level_1}, {postal_code}, {country}',
                                ]),
                           
                            // Forms\Components\TextInput::make('coordinates')
                            //     ->extraInputAttributes([
                            //         'data-google-field' => '{latitude}, {longitude}',
                            //     ]),
                            ]),
                        // Forms\Components\Card::make('Distance')
                        // ->schema([
                        //     TextInput::make('coordinates_to')
                        //         ->label('Coordinates To')
                        //         ->reactive(), // Reactively update distance when this changes
                
                        //     TextInput::make('coordinates_from')
                        //         ->label('Coordinates From')
                        //         ->reactive(), // Reactively update distance when this changes
                
                        //     TextInput::make('distance')
                        //         ->label('Distance')
                        //         ->disabled(), // Disable editing the distance, calculated automatically
                        //     TextInput::make('zone')
                        //         ->label('Travel EBA Zone')
                        //         ->disabled(),
                        // ])
                        // ->columns(2)
                        // ->afterStateUpdated(function (array $state, callable $set) {
                        //     // Get coordinates as comma-separated strings "lat,lng"
                        //     $coordinatesTo = explode(',', $state['coordinates_to']);
                        //     $coordinatesFrom = explode(',', $state['coordinates_from']);
                
                        //     if (count($coordinatesTo) == 2 && count($coordinatesFrom) == 2) {
                        //         // Convert latitude and longitude to float
                        //         $lat1 = floatval($coordinatesTo[0]);
                        //         $lng1 = floatval($coordinatesTo[1]);
                        //         $lat2 = floatval($coordinatesFrom[0]);
                        //         $lng2 = floatval($coordinatesFrom[1]);
                
                        //         // Calculate the distance using the Haversine formula
                        //         $distance = self::calculateHaversineDistance($lat1, $lng1, $lat2, $lng2);
                        //         if ($distance < 10) {
                        //             $zone = 'ZONE -1';
                        //         } elseif ($distance >= 10 && $distance <= 50) {
                        //             $zone = 'ZONE -2';
                        //         } else {
                        //             $zone = 'ZONE -3';
                        //         }
                        //         // Set the distance in the form
                        //         $set('distance', round($distance, 3) . ' km');
                        //         $set('zone', $zone);
                        //     } else {
                        //         $set('distance', 'Invalid coordinates');
                        //         $set('zone', null);
                        //     }
                        // }),
                        
                        
                    Textarea::make('notes')
                        ->label('Notes')
                        ->rows(3)
                        ->nullable()
                        ->columnSpanFull(),
                ])->collapsible()
                ,
                
                
            // Line Items Repeater in a separate Card
            Forms\Components\Card::make('Material')
            
                ->schema([
                    Repeater::make('lineItems')
                        ->relationship()
                        ->label('Material Items')
                        ->deletable(function (callable $get) {
                            // Return false if the count of line items is equal to 1
                            return count($get('lineItems') ?? []) !== 1; 
                        })
                        ->reorderable(true)
                        ->schema([
                        Select::make('description')
                                ->label('Description')
                                ->required()
                                ->reactive()
                                ->helperText(function (callable $get) {
                                    // Check if 'supplier_id' is empty and display helper text with red text
                                    return empty($get('../../supplier_id')) 
                                        ? new HtmlString('<span style="color:red;">Please select supplier before searching</span>')
                                        : null;
                                })
                                
                                ->options(function (callable $get) {
                                    $supplierId = $get('../../supplier_id');
                                    $lineItems = $get('../../lineItems') ?? [];
                                    $selectedCodes = collect($lineItems)->pluck('item_code')->filter()->toArray();
                                    
                                    if (empty($supplierId)) {
                                        return [];
                                    }
                            
                                    // Grouped options example
                                    return [
                                        'Favourite' => MaterialItem::when($supplierId, function ($query) use ($supplierId) {
                                                $query->where('supplier_name', $supplierId); // Assuming 'supplier_name' is correct
                                            })
                                            ->when($selectedCodes, function ($query) use ($selectedCodes) {
                                                $query->whereNotIn('code', $selectedCodes); // Exclude already selected item codes
                                            })
                                            ->where('is_favourite', true)
                                            ->pluck('description', 'description')
                                            ->toArray(),
                                        
                                        'All' =>MaterialItem::when($supplierId, function ($query) use ($supplierId) {
                                            $query->where('supplier_name', $supplierId); // Assuming 'supplier_name' is correct
                                        })
                                        ->when($selectedCodes, function ($query) use ($selectedCodes) {
                                            $query->whereNotIn('code', $selectedCodes); // Exclude already selected item codes
                                        })
                                        ->pluck('description', 'description')
                                        ->toArray(),
                                    ];
                                })
                                
                                
                                // (function (callable $get) {
                                    
                                //     // Log::info('Supplier Name:', ['supplier_name' => $get('../../supplier_id')]);
                                //     $supplierId = $get('../../supplier_id');
                                //     $lineItems = $get('../../lineItems') ?? [];
                                //     $selectedCodes = collect($lineItems)->pluck('item_code')->filter()->toArray();
                                //     // Check if supplier_id is present and filter MaterialItem based on the supplier
                                //     return MaterialItem::when($supplierId, function ($query) use ($supplierId) {
                                //         $query->where('supplier_name', $supplierId); // Assuming 'supplier_id' is the foreign key
                                //     })
                                //     ->when($selectedCodes, function ($query) use ($selectedCodes) {
                                //         $query->whereNotIn('code', $selectedCodes); // Exclude already selected item codes
                                //     })
                                //     ->pluck('description', 'description')
                                //     ->toArray();
                                // })
                                ->columnspan(4)
                                ->searchable()
                                ->required()
                                ->disabled(function (callable $get) {
                                    // Disable the select field if supplier_id is null
                                    return is_null($get('../../supplier_id'));
                                })
                                ->afterStateUpdated(function ($state, callable $set) {
                                    // Fetch the associated description when item_code is selected
                                    $materialItem = MaterialItem::where('description', $state)->first();
                                    if ($materialItem) {
                                        // Populate the description if the item exists
                                        $set('item_code', $materialItem->code); // Set the description directly
                                    } else {
                                        $set('description', null); // Clear the description if no item found
                                    }
                                }),  
                            Select::make('item_code')
                                ->label('Item Code')
                                ->required()
                                ->reactive()
                                ->helperText(function (callable $get) {
                                    // Check if 'supplier_id' is empty and display helper text with red text
                                    return empty($get('../../supplier_id')) 
                                        ? new HtmlString('<span style="color:red;">Please select supplier before searching</span>')
                                        : null;
                                })
                                
                                ->options(function (callable $get) {
                                    
                                    // Log::info('Supplier Name:', ['supplier_name' => $get('../../supplier_id')]);
                                    $supplierId = $get('../../supplier_id');
                                    $lineItems = $get('../../lineItems') ?? [];
                                    $selectedCodes = collect($lineItems)->pluck('item_code')->filter()->toArray();
                                    // Check if supplier_id is present and filter MaterialItem based on the supplier
                                    return MaterialItem::when($supplierId, function ($query) use ($supplierId) {
                                        $query->where('supplier_name', $supplierId); // Assuming 'supplier_id' is the foreign key
                                    })
                                    ->when($selectedCodes, function ($query) use ($selectedCodes) {
                                        $query->whereNotIn('code', $selectedCodes); // Exclude already selected item codes
                                    })
                                    ->pluck('code', 'code')
                                    ->toArray();
                                })
                               
                                ->searchable()
                                ->required()
                                ->columnspan(3)
                                ->disabled(function (callable $get) {
                                    // Disable the select field if supplier_id is null
                                    return is_null($get('../../supplier_id'));
                                })
                                ->afterStateUpdated(function ($state, callable $set) {
                                    // Fetch the associated description when item_code is selected
                                    $materialItem = MaterialItem::where('code', $state)->first();
                                    if ($materialItem) {
                                        // Populate the description if the item exists
                                        $set('description', $materialItem->description); // Set the description directly
                                    } else {
                                        $set('description', null); // Clear the description if no item found
                                    }
                                }),
                                
                            
                              
                            TextInput::make('qty')
                                ->label('Quantity (ea)')
                                ->default(1)
                                ->required()
                                ->columnspan(2)
                                ->numeric(),
                            MoneyInput::make('cost')->decimals(6)->columnspan(2)->default(0.000000)->currency('AUD'),
                            // TextInput::make('cost')
                            //     ->label('Cost (ea)')
                            //     ->default(0)
                            //     ->columnspan(2)
                            //     ->numeric(),
                        ])
                        ->addActionLabel('Add item')
                        
                        ->minItems(1) // Require at least one line item
                        ->columns(11), // 4-column layout for the repeater
                        
                ])
               
                ->collapsible(),
                
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                
                // ToggleColumn::make('is_processed')->afterStateUpdated(function (Requisition $record, bool $state) {
                //     // Toggle is_processed status
                //     $record->update(['is_processed' => $state]);
            
                //     // Send notification after toggling
                //     Notification::make()
                //         ->title('Requisition Updated')
                //         ->body($state ? 'Requisition has been processed.' : 'Requisition has been marked as unprocessed.')
                //         ->success() // Or you can use .danger() for error messages
                //         ->send();
                // }),
               
                TextColumn::make('requisition_number')->label('Req #')->sortable(),
                
                TextColumn::make('project_id')->sortable()->label('Project')->getStateUsing(function ($record) {
                    $project = \App\Models\Project::find($record->project_id);
                    return $project ? $project->name : 'N/A'; // Return 'N/A' if project is not found
                }),
                TextColumn::make('supplier_id')->sortable(),
                TextColumn::make('deliver_to')->sortable()->limit(20) // Limit the text to 50 characters
                ->wrap() // Optional: Wrap the text if needed
                ->tooltip(fn ($record) => $record->deliver_to),
                TextColumn::make('created_at')
                ->label('Submitted on')
                ->sortable()
                
                ->formatStateUsing(fn ($state) => Carbon::parse($state)->diffForHumans()),
                
                   
                TextColumn::make('pickup_by')->sortable(),
                
            ])
            ->filters([
                Filter::make('is_processed')
                ->label('pending')
                ->query(fn (Builder $query): Builder => $query->where('is_processed', false))
                ->default(),
                TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->iconButton()->tooltip('Edit Requisition'),
                Action::make('markProcessed')
                    ->tooltip(fn (Requisition $record): string => $record->is_processed ? 'Unmark as processed' : 'Mark as processed')
                    ->icon('heroicon-o-check-circle')
                    ->iconButton()
                    ->action(function (Requisition $record): void {
                        $record->is_processed = !$record->is_processed;  // Toggle the value
                        $record->save();  // Save the updated record
                    })
                    ->color(fn (Requisition $record): string => $record->is_processed ? 'success' : 'gray')
                    ->visible(fn (): bool => Auth::check() && Auth::user()->isAdmin()),
                Tables\Actions\DeleteAction::make()->iconButton()->tooltip('Delete Requisition')->requiresConfirmation(),
                
                Action::make('download')
                    ->icon('heroicon-o-document')
                    ->iconButton()
                    ->size(ActionSize::Small)
                    ->tooltip('Download as PDF')
                    ->url(fn (Requisition $record): string => route('requisition.pdf', ['requisition' => $record->id]))
                    ->openUrlInNewTab(),
                Action::make('upload items')->tooltip('Upload csv to add material items')->icon('heroicon-o-arrow-up-tray')->iconButton()
                    ->form([
                        
                        FileUpload::make('upload_csv')
                        ->required()
                        ->acceptedFileTypes(['text/csv'])
                        ->label('Csv file must have the headers "item_code", "description", "qty", "cost" - Excel is not supported. Uploading items will replace any existing items - proceed with caution.')
                        
                    ])
                    ->action(function (array $data, Requisition $record): void {
                        // Check if the file is uploaded
                        Log::info($data['upload_csv']);
                        $fileName = $data['upload_csv'];
                        $path = storage_path("app/public/{$fileName}");
                        if (isset($data['upload_csv']) && !empty($data['upload_csv'])) {
                            // Construct the path to the uploaded file
                            $fileName = $data['upload_csv'];
                            $path = storage_path("app/public/{$fileName}");
                
                            // Check if the file exists
                            if (file_exists($path)) {
                                // Read the CSV file
                                $csvData = array_map('str_getcsv', file($path));
                                if (count($csvData) > 0) {
                                    // Get headers from the first row
                                    // $headers = array_shift($csvData); // Remove and get the header row
                                    $headers = array_map('trim', array_shift($csvData));
                                    // Define the expected headers
                                    $expectedHeaders = ['item_code', 'description', 'qty', 'cost'];
                                // Check if headers match the expected headers
                                if ($headers) {
                                    Log::info('CSV headers are correct:', $headers);

                                    // Optionally log the CSV data
                                    Log::info('CSV Data:', $csvData);
                                    
                                    // Clear existing line items
                                    $record->lineItems()->delete(); 

                                    $newLineItems = [];

                                    // Loop through each row of the CSV data
                                    foreach ($csvData as $row) {
                                        // Assuming the CSV structure is correct
                                        $itemCode = $row[0]; // First column: item_code
                                        $description = $row[1]; // Second column: description
                                        $qty = $row[2]; // Third column: qty
                                        $cost = $row[3]; // Fourth column: cost

                                        // Create new line item
                                        $newLineItems[] = [
                                            'requisition_id' => $record->id, // Foreign key
                                            'item_code' => $itemCode,
                                            'description' => $description,
                                            'qty' => $qty,
                                            'cost' => $cost,
                                        ];
                                    }

                                    // Create new line items in bulk
                                    $record->lineItems()->createMany($newLineItems);
                                    activity()
                                    ->performedOn($record)
                                    ->event('csv upload')
                                    ->causedBy(auth()->user()) // Assuming you have user authentication
                                    ->log('Uploaded CSV items for requisition: ' . $record->id);
                                    unlink($path);
                                    // Save the requisition record if needed
                                    $record->save();
                                } else {
                                    Log::warning('CSV headers do not match expected headers.', [
                                        'expected' => $expectedHeaders,
                                        'actual' => $headers,
                                    ]);
                                }
                            } else {
                                Log::warning('CSV file is empty.');
                            }
                        } else {
                            // Handle the case where the file does not exist
                            Log::warning('CSV file does not exist at the specified path: ' . $path);
                        }
                        
                    } else {
                        // Handle the case where no file was uploaded
                        Log::warning('No CSV file uploaded.');
                                    }
                                }) ->visible(fn () => Auth::user()->can('upload_requisition'))
                                ,
                            // ReplicateAction::make(),
                            
                            Action::make('Duplicate')
                            
                                ->icon('heroicon-s-document-duplicate')
                                ->tooltip('Create a copy of the requisition')
                                ->action(fn (Requisition $requisition) => self::replicateRequisition($requisition))
                                ->color('warning')
                                ->requiresConfirmation()->iconButton(),
                           RestoreAction::make(),  // Option to restore soft-deleted records
                           Action::make('markProcessed')
                                ->tooltip(fn (Requisition $record): string => $record->is_processed ? 'Remove from processed' : 'Mark as processed'  )
                                ->icon('heroicon-s-check-circle')
                                ->iconButton()
                                ->action(function (Requisition $record): void {
                                    $record->is_processed = !$record->is_processed;  // Toggle the value
                                    $record->save();  // Save the updated record
                                })
                                ->color(fn (Requisition $record): string => $record->is_processed ? 'success' : 'gray')
                                ->visible(fn () => Auth::user()->can('process_requisition'))
                                ->disabled(fn (Requisition $record) => $record->is_processed && !Auth::user()->can('unprocess_requisition'))
                                ->requiresConfirmation(),
                               
                                
                                    ])
                        ->bulkActions([
                                        Tables\Actions\BulkActionGroup::make([
                                            Tables\Actions\DeleteBulkAction::make(),
                                            
                                        ]),
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
            'index' => Pages\ListRequisitions::route('/'),
            'create' => Pages\CreateRequisition::route('/create'),
            'edit' => Pages\EditRequisition::route('/{record}/edit'),
            'view' => Pages\ViewRequisition::route('{record}/view'),
        ];
    }
    public static function calculateHaversineDistance($lat1, $lon1, $lat2, $lon2)
{
    $earthRadius = 6371; // Radius of the earth in km

    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a = sin($dLat / 2) * sin($dLat / 2) +
        cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
        sin($dLon / 2) * sin($dLon / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    $distance = $earthRadius * $c; // Distance in km

    return $distance;
}
public static function replicateRequisition(Requisition $requisition): void
{
    // Replicate the project instance
    $newrequisition = $requisition->replicate();
    $newrequisition->is_processed = false; // Set is_processed to false
    $newrequisition->date_required = now();
    $newrequisition->save();

    // Replicate related tasks
    foreach ($requisition->lineItems as $item) {
        $newItem = $item->replicate();
        $newItem->requisition_id = $newrequisition->id; // Set the foreign key
        $newItem->save();
    }
}
}
