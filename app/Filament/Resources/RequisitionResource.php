<?php

namespace App\Filament\Resources;

use Closure;
use Carbon\Carbon;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Project;
use Filament\Forms\Get;
use App\Models\ItemBase;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Requisition;
use Illuminate\Support\Str;
use App\Models\MaterialItem;
use App\Models\ItemBasePrice;
use App\Models\ItemProjectPrice;
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ImportAction;
use Illuminate\Support\HtmlString;
use App\Models\RequisitionLineItem;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Tabs;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Wizard;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Facades\Cache;
use Filament\Support\Enums\ActionSize;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Navigation\NavigationItem;
use Filament\Panel\Concerns\HasAvatars;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Filters\TrashedFilter;
use LaraZeus\Popover\Tables\PopoverColumn;
use App\Notifications\RequisitionProcessed;
use Filament\Tables\Columns\CheckboxColumn;
// use App\Filament\Resources\RequisitionResource\Widgets\StatsOverview;
use Filament\Tables\Actions\ReplicateAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\AvatarProviders\UiAvatarsProvider;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RequisitionResource\Pages;
use Filament\AvatarProviders\Contracts\AvatarProvider;
use App\Notifications\RequisitionProcessedNotification;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use App\Filament\Resources\RequisitionResource\RelationManagers;
use Filament\Notifications\Actions\Action as NotificationAction;
use Tapp\FilamentGoogleAutocomplete\Forms\Components\GoogleAutocomplete;
use App\Filament\Resources\RequisitionResource\RelationManagers\AttachmentsRelationManager;

class RequisitionResource extends Resource implements HasShieldPermissions
{
    use HasAvatars;
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
            'upload',
            'view_all_requisitions',
        ];
    }
    public static function boot()
    {
        // Set the default avatar provider for the resource
        static::defaultAvatarProvider(UiAvatarsProvider::class);
    }
    protected static ?string $model = Requisition::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';

    protected static ?string $navigationGroup = 'Main';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        // If the user has either the 'office_admin' or 'super_admin' role
        if ($user->hasRole(['office_admin', 'super_admin'])) {
            // Show all records without filtering
            return parent::getEloquentQuery();
        }


        // If the user is a Superadmin, show all records without filtering
        return parent::getEloquentQuery()->where('created_by', $user->id);
    }
    public static function getNavigationBadge(): ?string
    {
        return number_format(static::getModel()::where('is_processed', false)->count());
    }
    public static function canEdit($record): bool
    {
        return !$record->is_processed; // Disable editing if is_processed is true
    }
    public static function processCsvUpload(array $data, Requisition $record)
    {
        $path = storage_path("app/public/{$data['upload_csv']}");

        if (!file_exists($path) || !($csvData = array_map('str_getcsv', file($path)))) {
            Log::warning(file_exists($path) ? 'CSV file is empty.' : 'CSV file does not exist: ' . $path);
            return false;
        }

        $headers = array_shift($csvData); // Skip header row
        $newLineItems = [];

        foreach ($csvData as $row) {
            // Assuming static::getMaterialFromState is the method to fetch material
            $material = static::getMaterialFromState($row[0], $record->project_id);
            $newLineItems[] = [
                'requisition_id' => $record->id,
                'item_code' => $material->code ?? 'upload_failed',
                'description' => $material->description ?? 'upload_failed',
                'qty' => $row[2],
                'cost' => $material->cost ?? 0,
                'price_list' => $material->price_list ?? 'NA',
            ];
        }

        if (!empty($newLineItems)) {
            $record->lineItems()->delete();
            $record->lineItems()->createMany($newLineItems);
            unlink($path); // Delete the CSV file after processing
            $record->save();

            activity()
                ->performedOn($record)
                ->event('csv upload')
                ->causedBy(auth()->user())
                ->log('Uploaded CSV items for requisition: ' . $record->id);

            return true;
        }

        return false;
    }


    public static function getMaterialFromState($state, $project_id): ?object
    {
        // Generate a cache key based on the state and project_id
        $cacheKey = "material_{$state}_project_{$project_id}";

        // Check if the value is cached
        $materialData = Cache::get($cacheKey);

        // If not cached, retrieve and cache the result
        if (!$materialData) {
            $material = MaterialItem::where('code', $state)
                ->orWhere('description', $state)
                ->first();

            if ($material) {
                $projectNumber = Project::find($project_id)->site_reference;
                $itemProjectPrice = ItemProjectPrice::where('item_code', $material->code)
                    ->where('project_number', $projectNumber)
                    ->first(['price_list', 'price']);

                // Prepare the material data for caching
                if ($itemProjectPrice) {
                    $materialData = (object) [
                        'code' => $material->code,
                        'description' => $material->description,
                        'cost' => $itemProjectPrice->price,
                        'price_list' => $itemProjectPrice->price_list,
                    ];
                } else {
                    $baseprice = ItemBasePrice::whereHas('base', function ($query) {
                        $query->where('effective_from', '<=', now()->today())
                            ->where('effective_to', '>=', now()->today());
                    })->where('material_item_code', $material->code)->first()?->price;

                    $materialData = (object) [
                        'code' => $material->code,
                        'description' => $material->description,
                        'cost' => $baseprice ?? 0.0000,
                        'price_list' => $baseprice ? 'base_price' : null,
                    ];
                }

                // Cache the result for 60 minutes (adjust as needed)
                Cache::put($cacheKey, $materialData, now()->addMonths(3));
            } else {
                $materialData = null;
            }
        }

        return $materialData;
    }

    public static function form(Form $form): Form
    {
        return $form


            ->schema([
                Wizard::make([

                    Wizard\Step::make('Add Items')
                        ->schema([
                            Select::make('project_id')
                                ->label('Project')
                                ->options(function () {
                                    // Cache the options for 3 hours to avoid querying the database on each request
                                    return Cache::remember('projects_options', now()->addHours(3), function () {
                                        // Retrieve the list of projects, using 'id' as the key and 'name' as the value
                                        $user = Auth::user();
                                        if ($user) {
                                            return $user->projects->pluck('name', 'id')->toArray();
                                        }
                                        // return Project::query()
                                        //     ->pluck('name', 'id')
                                        //     ->toArray();
                                        return [];
                                    });
                                })->searchable()
                                ->reactive()
                                ->afterStateUpdated(function (callable $set, $state) {
                                    // Try to retrieve the project from the cache using the project ID as the key
                                    $project = Cache::remember("project_{$state}", now()->addHours(3), function () use ($state) {
                                        return Project::find($state);  // Retrieve project from the database if not cached
                                    });

                                    // Define the fields to be updated
                                    $fields = [
                                        'deliver_to',
                                        'delivery_contact',
                                        'coordinates',
                                        'site_reference',
                                        'pickup_by',
                                        'requested_by',
                                        'notes',
                                    ];

                                    // Set the project data if found, otherwise reset to null
                                    foreach ($fields as $field) {
                                        $set($field, $project->$field ?? null);
                                    }
                                }),
                            Select::make('supplier_name')
                                ->label('Supplier')
                                ->required()
                                ->columnSpan(1)
                                ->options(function () {
                                    // Cache the supplier names for 3 hours to avoid querying the database on each request
                                    return Cache::remember('supplier_names', now()->addHours(3), function () {
                                        // Retrieve the distinct supplier names and cache the result
                                        return MaterialItem::query()
                                            ->select('supplier_name')
                                            ->distinct()
                                            ->pluck('supplier_name', 'supplier_name')
                                            ->toArray();
                                    });
                                })
                                ->disabled(function (callable $get) {
                                    // Disable the select field if supplier_id is null
                                    return is_null($get('project_id'));
                                })
                                ->searchable() // Add searchable to enhance UX,
                                ->reactive()
                                ->afterStateUpdated(function (callable $set) {
                                    $set('lineItems.*.item_code', null); // Clear item_code in line items if needed
                                    $set('lineItems.*.description', null); // Clear description in line items if needed
                                    $set('lineItems.*.cost', null); // Clear description in line items if needed
                                    $set('lineItems.*.price_list', null); // Clear description in line items if needed
                                    $set('lineItems.*.qty', 1); // Clear description in line items if needed
                                }),
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
                                        ->selectablePlaceholder(false)
                                        ->helperText(function (callable $get) {
                                            // Check if 'supplier_id' is empty and display helper text with red text
                                            return empty($get('../../supplier_name'))
                                                ? new HtmlString('<span style="color:red;">Please select supplier before searching</span>')
                                                : null;
                                        })

                                        ->options(function (callable $get) {
                                            $supplierId = $get('../../supplier_name');
                                            $lineItems = $get('../../lineItems') ?? [];
                                            $selectedCodes = collect($lineItems)->pluck('item_code')->filter()->toArray();
                                            $project = Project::find($get('../../project_id'));
                                            if (empty($supplierId)) {
                                                return [];
                                            }

                                            // Grouped options example
                                            return [
                                                'Favourite Material for Project' => $project->materials()->pluck('description', 'description')->toArray(),
                                                'Favourite' => MaterialItem::when($supplierId, function ($query) use ($supplierId) {
                                                $query->where('supplier_name', $supplierId); // Assuming 'supplier_name' is correct
                                            })
                                                    ->when($selectedCodes, function ($query) use ($selectedCodes) {
                                                $query->whereNotIn('code', $selectedCodes); // Exclude already selected item codes
                                            })
                                                    ->where('is_favourite', true)
                                                    ->pluck('description', 'description')
                                                    ->toArray(),

                                                'All' => MaterialItem::when($supplierId, function ($query) use ($supplierId) {
                                                $query->where('supplier_name', $supplierId); // Assuming 'supplier_name' is correct
                                            })
                                                    ->when($selectedCodes, function ($query) use ($selectedCodes) {
                                                $query->whereNotIn('code', $selectedCodes); // Exclude already selected item codes
                                            })
                                                    ->pluck('description', 'description')
                                                    ->toArray(),


                                            ];
                                        })



                                        ->columnSpan([
                                            'default' => 1,
                                            'sm' => 2,
                                            'xl' => 4,

                                        ])
                                        ->searchable()
                                        ->required()
                                        ->disabled(function (callable $get) {
                                            // Disable the select field if supplier_id is null
                                            return is_null($get('../../supplier_name'));
                                        })
                                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                            // Fetch the associated description when item_code is selected
                                            $project_id = $get('../../project_id');
                                            $material = static::getMaterialFromState($state, $project_id);


                                            if ($material) {
                                                // Populate the description if the item exists
                                                $data = [
                                                    'qty' => 1,
                                                    'description' => $material->description,
                                                    'item_code' => $material->code,
                                                    'price_list' => $material->price_list,
                                                    'cost' => $material->cost
                                                ];

                                                foreach ($data as $field => $value) {
                                                    $set($field, $value);
                                                }



                                            } else {
                                                $data = [
                                                    'qty' => 1,
                                                    'description' => '',
                                                    'item_code' => null,
                                                    'price_list' => null,
                                                    'cost' => null
                                                ];
                                                foreach ($data as $field => $value) {
                                                    $set($field, $value);
                                                }
                                            }
                                        }),


                                    Select::make('item_code')
                                        ->label('Item Code')
                                        ->required()
                                        ->reactive()
                                        ->selectablePlaceholder(false)
                                        ->helperText(function (callable $get) {
                                            // Check if 'supplier_id' is empty and display helper text with red text
                                            return empty($get('../../supplier_name'))
                                                ? new HtmlString('<span style="color:red;">Please select supplier before searching</span>')
                                                : null;
                                        })

                                        ->options(function (callable $get) {

                                            // Log::info('Supplier Name:', ['supplier_name' => $get('../../supplier_id')]);
                                            $supplierId = $get('../../supplier_name');
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
                                        ->columnSpan([
                                            'default' => 1,
                                            'xl' => 2,
                                            '2xl' => 3,
                                        ])
                                        ->disabled(function (callable $get) {
                                            // Disable the select field if supplier_id is null
                                            return is_null($get('../../supplier_name'));

                                        })
                                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                            // Fetch the associated description when item_code is selected
                                            $project_id = $get('../../project_id');
                                            $material = static::getMaterialFromState($state, $project_id);


                                            if ($material) {
                                                // Populate the description if the item exists
                                                $data = [
                                                    'qty' => 1,
                                                    'description' => $material->description,
                                                    'item_code' => $material->code,
                                                    'price_list' => $material->price_list,
                                                    'cost' => $material->cost
                                                ];

                                                foreach ($data as $field => $value) {
                                                    $set($field, $value);
                                                }



                                            } else {
                                                $data = [
                                                    'qty' => 1,
                                                    'description' => null,
                                                    'item_code' => null,
                                                    'price_list' => null,
                                                    'cost' => null
                                                ];
                                                foreach ($data as $field => $value) {
                                                    $set($field, $value);
                                                }
                                            }
                                        }),

                                    TextInput::make('qty')
                                        ->label('Quantity (ea)')
                                        ->default(1)
                                        ->required()
                                        ->placeholder('Enter quantity')
                                        ->columnSpan([
                                            'default' => 1,
                                            'xl' => 1,
                                        ])

                                        ->numeric(),
                                    TextInput::make('cost')->numeric()->columnspan(1)->readOnly(),
                                    TextInput::make('price_list')->columnspan(1)->readOnly(),


                                ])
                                ->addActionLabel('Add item')

                                ->minItems(1) // Require at least one line item
                                ->columns([
                                    'default' => 1,
                                    'sm' => 5,
                                    'xl' => 10,

                                ]), // 4-column layout for the repeater

                        ]),
                    Wizard\Step::make('Delivery Details')
                        ->schema([
                            Grid::make(2) // Organize inputs in 3 columns
                                ->schema([

                                    DatePicker::make('date_required')
                                        ->label('Date Required')
                                        ->required()

                                        ->columnSpan(1),
                                    // ->minDate(now()->addDay(0)),
                                    TimePicker::make('pickup_time')->default('10:00')->withoutSeconds()->label('Delivery Time'),
                                    TextInput::make('site_reference')
                                        ->label('Site Reference')
                                        ->required()
                                        ->columnSpan(1)
                                        ->readOnly(),

                                    TextInput::make('delivery_contact')
                                        ->label('Delivery Contact')
                                        ->required()
                                        ->columnSpan(1),
                                ]),
                            Grid::make(2)
                                ->schema([
                                    Grid::make(1)->schema([
                                        TextInput::make('pickup_by')
                                            ->label('Pickup By')
                                            ->required()
                                            ->columnSpan(1),
                                        TextInput::make('requested_by'),
                                        Forms\Components\TextInput::make('deliver_to'),
                                    ])->columnSpan(1),
                                    RichEditor::make('notes')
                                        ->fileAttachmentsDisk('s3')
                                        ->fileAttachmentsDirectory('/requisitions/attachments')
                                        ->fileAttachmentsVisibility('private')
                                        ->toolbarButtons([
                                            'bold',
                                            'bulletList',
                                            'heading',
                                            'italic',
                                            'redo',
                                            'undo',
                                            'attachFiles'
                                        ])
                                ]),
                            Repeater::make('attachments')
                                ->relationship()
                                ->label('Attachments')

                                ->addActionAlignment(Alignment::End)
                                ->deletable()
                                ->reorderable(true)
                                ->schema([
                                    FileUpload::make('file_path')->disk('s3')->preserveFilenames()->directory('requisitions/attachments')->label('Attachment')->visibility('publico'),
                                ])->defaultItems(0),
                        ]),
                ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('requisition_number')->label('Req #')->sortable()->badge(),
                TextColumn::make('project_id')->sortable()->label('Job')
                    ->getStateUsing(function ($record) {
                        $project = \App\Models\Project::find($record->project_id);
                        return $project ? $project->name : 'N/A'; // Return 'N/A' if project is not found
                    }),
                TextColumn::make('supplier_name')->sortable(),
                \LaraZeus\Popover\Tables\PopoverColumn::make('creator.name')
                    ->searchable()

                    ->label('')
                    ->content(fn($record) => view('components.user-card', ['record' => $record]))
                    ->formatStateUsing(function ($record) {
                        return view('components.user-detail', ['record' => $record])->render();
                    })
                    ->html()
                    ->extraHeaderAttributes([
                        'class' => 'w-16'
                    ])
                    // main options
                    ->trigger('hover') // support click and hover
                    ->placement('right') // for more: https://alpinejs.dev/plugins/anchor#positioning
                    ->offset(0) // int px, for more: https://alpinejs.dev/plugins/anchor#offset
                    ->popOverMaxWidth('none')
                    // ->icon('heroicon-o-chevron-right') // show custom icon
                    ->content(fn($record) => view('components.user-card', ['record' => $record])),
            ])
            ->filters([
                Filter::make('is_processed')
                    ->label('Pending')
                    ->query(fn(Builder $query): Builder => $query->where('is_processed', false))
                    ->toggle()
                    ->default(),
                SelectFilter::make('supplier_name')
                    ->label('Supplier Name')
                    ->options(
                        MaterialItem::pluck('supplier_name', 'supplier_name')->unique() // Adjust based on your column names
                    ),
                SelectFilter::make('created_by')
                    ->label('Submitted By')
                    ->options(
                        User::pluck('name', 'id')->unique()->map(function ($name) {
                            return Str::title($name); // Apply proper case to each name
                        }) // Fetch user names and IDs
                    ),
                SelectFilter::make('project_id')
                    ->label('Project')
                    ->options(
                        Project::pluck('name', 'id')->unique() // Fetch user names and IDs
                    ),

                TrashedFilter::make(),

            ])
            ->actions([
                Tables\Actions\DeleteAction::make()->iconButton()->tooltip('Delete Requisition')->requiresConfirmation()->visible(fn(): bool => Auth::check() && Auth::user()->role('super_admin')),

                Action::make('upload items')
                    ->tooltip('Upload csv to add material items')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->iconButton()
                    // ->hidden(fn($record): bool => $record->is_processed)
                    ->form([
                        FileUpload::make('upload_csv')
                            ->required()
                            ->acceptedFileTypes(['text/csv'])
                            ->label(
                                'Csv file must have the headers "item_code", "description", "qty", "cost" - Excel is not supported. Uploading items will replace any existing items - proceed with caution.'
                            ),
                    ])
                    ->action(function (array $data, Requisition $record): void {
                        // Call the service method for processing the CSV upload
                        $success = RequisitionResource::processCsvUpload(
                            $data,
                            $record
                        );

                        if ($success) {
                            // Handle success logic, e.g., show a success message
                        } else {
                            // Handle failure, e.g., log or show an error message
                        }
                    })
                    ->visible(fn() => Auth::user()->can('upload_requisition')),
                Action::make('markProcessed')
                    ->tooltip(fn(Requisition $record): string => $record->is_processed ? 'Remove from processed' : 'Mark as processed')
                    ->icon('heroicon-s-check-circle')
                    ->iconButton()
                    ->action(function (Requisition $record): void {
                        $record->is_processed = !$record->is_processed;  // Toggle the value
                        $record->processed_by = Auth::id(); // Set the processed_by field to the authenticated user's ID
                        $record->processed_at = now();
                        $link = url("/admin/requisitions/{$record->id}/view");
                        $record->save();  // Save the updated record
                        if ($record->creator) {
                            // Get the formatted processed_at time
                            $processedAt = Carbon::parse($record->processed_at);
                            $now = now('Australia/Brisbane');

                            // Determine the formatted message
                            if ($processedAt->isToday()) {
                                $timeMessage = 'Today at ' . $processedAt->format('g:i A');
                            } elseif ($processedAt->isYesterday()) {
                                $timeMessage = 'Yesterday at ' . $processedAt->format('g:i A');
                            } elseif ($processedAt->diffInDays($now) <= 7) {
                                $timeMessage = $processedAt->diffInDays($now) . ' days ago at ' . $processedAt->format('g A');
                            } else {
                                $timeMessage = 'On ' . $processedAt->format('jS F, Y') . ' at ' . $processedAt->format('g A');
                            }
                            $record->creator->notify(new RequisitionProcessedNotification($record));
                            $record->creator->notify(
                                Notification::make()
                                    ->title('Requisition Processed: ' . $record->requisition_number) // Title with requisition number
                                    ->success() // Mark as a success notification
                                    ->body('The requisition has been ' . ($record->is_processed ? 'processed' : 'unprocessed') . ' by ' . Auth::user()->name . ' ' . $timeMessage)
                                    // Save it to the database
                                    ->actions([
                                        NotificationAction::make('view') // Create a new action named 'view'
                                            ->url($link) // Set the URL for the action
                                            ->button() // Make it a button   
                                    ])
                                    ->toDatabase(),
                            );
                        } else {
                            Log::warning('No creator found for the requisition:', ['requisition_id' => $record->id]);
                        }


                    })
                    ->color(fn(Requisition $record): string => $record->is_processed ? 'success' : 'gray')
                    ->visible(fn() => Auth::user()->can('process_requisition'))
                    ->disabled(fn(Requisition $record) => $record->is_processed && !Auth::user()->can('unprocess_requisition')),
                // ->requiresConfirmation(),
                ActionGroup::make([
                    ViewAction::make(),
                    Tables\Actions\EditAction::make()->tooltip('Edit Requisition'),
                    Action::make('Duplicate')
                        ->icon('heroicon-s-document-duplicate')
                        ->tooltip('Create a copy of the requisition')
                        ->action(fn(Requisition $requisition) => self::replicateRequisition($requisition))
                        ->color('warning')
                        ->requiresConfirmation(),
                    Action::make('download')
                        ->icon('heroicon-o-document')
                        ->size(ActionSize::Small)
                        ->tooltip('Download as PDF')
                        ->color('gray')
                        ->url(fn(Requisition $record): string => route('requisition.pdf', ['requisition' => $record->id]))
                        ->openUrlInNewTab(),

                    Action::make('download xlsx')
                        ->icon('heroicon-o-document')
                        ->size(ActionSize::Small)
                        ->color('gray')
                        ->url(fn(Requisition $record): string => route('requisition.excel', ['requisition' => $record->id]))
                        ->openUrlInNewTab(),
                    DeleteAction::make(),
                    RestoreAction::make(),  // Option to restore soft-deleted records
                ])

            ])
        ;
    }

    public static function getRelations(): array
    {
        return [
            // AttachmentsRelationManager::class,
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

    public static function replicateRequisition(Requisition $requisition): void
    {
        // Replicate the project instance
        $newrequisition = $requisition->replicate();
        $newrequisition->is_processed = false; // Set is_processed to false
        $newrequisition->processed_by = null;
        $newrequisition->updated_by = null;
        $newrequisition->processed_at = null;
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
