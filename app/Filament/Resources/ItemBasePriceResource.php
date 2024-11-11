<?php

namespace App\Filament\Resources;

use App\Filament\Imports\ItemBasePriceImporter;
use Filament\Forms;
use Filament\Tables;
use App\Models\ItemBase;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\MaterialItem;
use App\Models\ItemBasePrice;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\ImportAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ItemBasePriceResource\Pages;
use App\Filament\Resources\ItemBasePriceResource\RelationManagers;

class ItemBasePriceResource extends Resource
{
    protected static ?string $model = ItemBasePrice::class;
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $navigationLabel = 'Base Prices';
    protected static ?string $navigationGroup = 'Admin';

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('item_base_id')->options(
                    ItemBase::query()
                        ->pluck('name', 'id') // Correct order: 'name' as the value, 'id' as the key
                        ->toArray()
                )->searchable(),
                Select::make('material_item_id')->options(
                    MaterialItem::query()
                        ->pluck('code', 'id') // Correct order: 'name' as the value, 'id' as the key
                        ->toArray()
                )->searchable(),
                
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\TextInput::make('created_by')
                    ->numeric(),
                Forms\Components\TextInput::make('updated_by')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('base.name')
                    ->badge()
                   
                  
                    ->sortable(),
                Tables\Columns\TextColumn::make('item.description')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('item.code')
                ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->badge()
                    ->sortable(),
        
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(ItemBasePriceImporter::class)->label('Import')->tooltip('Update or Create Base Prices'),
                ])
            
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ])
                
                
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
            'index' => Pages\ListItemBasePrices::route('/'),
            'create' => Pages\CreateItemBasePrice::route('/create'),
            'edit' => Pages\EditItemBasePrice::route('/{record}/edit'),
        ];
    }
}
