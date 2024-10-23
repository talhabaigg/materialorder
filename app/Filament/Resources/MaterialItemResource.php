<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\MaterialItem;
use Filament\Resources\Resource;
use Filament\Pages\Actions\Action;
use App\Imports\MaterialItemsImport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ImportAction;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Exports\MaterialItemExporter;
use App\Filament\Imports\MaterialItemImporter;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\MaterialItemResource\Pages;
use App\Filament\Resources\MaterialItemResource\RelationManagers;
use Filament\Forms\Components\TextInput;

class MaterialItemResource extends Resource
{
    protected static ?string $model = MaterialItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
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
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Description')
                    ->sortable(),
                TextColumn::make('supplier_name')
                    ->label('Supplier')
                    ->sortable(),
                TextColumn::make('costcode')
                    ->label('Cost Code')
                    ->sortable(),
                
            ])
            ->filters([
                //
            ])
            ->actions([
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
