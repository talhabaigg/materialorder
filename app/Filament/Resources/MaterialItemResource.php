<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\MaterialItem;
use Filament\Resources\Resource;

use Filament\Tables\Actions\Action;
use App\Imports\MaterialItemsImport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
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
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Material Name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Description')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('supplier_name')
                    ->label('Supplier')
                    ->sortable(),
                TextColumn::make('costcode')
                    ->label('Cost Code')
                    ->sortable(),
                
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
                    ->exporter(MaterialItemExporter::class)->label('Export')
                    
                
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
