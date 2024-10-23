<?php

namespace App\Filament\Resources;

use Closure;
use Filament\Forms;
use Filament\Tables;
use App\Models\Project;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Requisition;
use App\Models\MaterialItem;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\ActionSize;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RequisitionResource\Pages;
use App\Filament\Resources\RequisitionResource\RelationManagers;
use App\Filament\Resources\RequisitionResource\Widgets\StatsOverview;
use Tapp\FilamentGoogleAutocomplete\Forms\Components\GoogleAutocomplete;

class RequisitionResource extends Resource
{
    protected static ?string $model = Requisition::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public $supplier;
    protected static ?string $navigationGroup = 'Collections';
    protected static ?int $navigationSort = 1;
    public static function getNavigationBadge(): ?string
    {
        return number_format(static::getModel()::where('is_processed', false)->count());
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
                    Grid::make(3) // Organize inputs in 3 columns
                        ->schema([
                            DatePicker::make('date_required')
                                ->label('Date Required')
                                ->required()
                                ->columnSpan(1)
                                ->minDate(now()->addDay(0)),
                                
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
                                    ->pluck('description', 'description')
                                    ->toArray();
                                })
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
                            TextInput::make('qty')
                                ->label('Quantity (ea)')
                                ->default(1)
                                ->required()
                                ->columnspan(2)
                                ->numeric(),
                                
                            TextInput::make('cost')
                                ->label('Cost (ea)')
                                ->default(0)
                                ->columnspan(2)
                                ->numeric(),
                        ])
                        ->addActionLabel('Add item')
                        ->minItems(1) // Require at least one line item
                        ->columns(11), // 4-column layout for the repeater
                ])
               
                ->collapsible()
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ToggleColumn::make('is_processed')->afterStateUpdated(function (Requisition $record, bool $state) {
                    // Toggle is_processed status
                    $record->update(['is_processed' => $state]);
            
                    // Send notification after toggling
                    Notification::make()
                        ->title('Requisition Updated')
                        ->body($state ? 'Requisition has been processed.' : 'Requisition has been marked as unprocessed.')
                        ->success() // Or you can use .danger() for error messages
                        ->send();
                })->sortable(),
               
                TextColumn::make('id')->label('#')->sortable(),
                TextColumn::make('project_id')->sortable()->label('Project')->getStateUsing(function ($record) {
                    $project = \App\Models\Project::find($record->project_id);
                    return $project ? $project->name : 'N/A'; // Return 'N/A' if project is not found
                }),
                TextColumn::make('supplier_id')->sortable(),
                TextColumn::make('deliver_to')->sortable(),
                
                
                   
                TextColumn::make('pickup_by')->sortable(),
            ])
            ->filters([
                Filter::make('is_processed')
                ->label('pending')
                ->query(fn (Builder $query): Builder => $query->where('is_processed', false))
                ->default(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->iconButton()
                    ->size(ActionSize::Small)
                    ->label('Download as PDF')
                    ->url(fn (Requisition $record): string => route('requisition.pdf', ['requisition' => $record->id]))
                    ->openUrlInNewTab(),
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
}
