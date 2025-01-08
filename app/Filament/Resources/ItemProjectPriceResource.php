<?php

namespace App\Filament\Resources;

use App\Filament\Imports\ItemProjectPriceImporter;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ItemProjectPrice;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ImportAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ItemProjectPriceResource\Pages;
use App\Filament\Resources\ItemProjectPriceResource\RelationManagers;

class ItemProjectPriceResource extends Resource
{
    protected static ?string $model = ItemProjectPrice::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
    protected static ?string $navigationGroup = 'Admin';
    protected static bool $shouldRegisterNavigation = false;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('price_list')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('item_code')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('project_number')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('price')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('price_list')
                    ->badge()
                    ->sortable()
                ,
                Tables\Columns\TextColumn::make('item_code')
                    ->searchable()
                    ->sortable()
                ,
                Tables\Columns\TextColumn::make('item.description')

                    ->sortable()
                ,
                Tables\Columns\TextColumn::make('projectlist.name')
                    ->label('Project')

                    ->sortable()
                ,
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(ItemProjectPriceImporter::class)->label('Import')->tooltip('Update or Create Project Prices'),
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
            'index' => Pages\ListItemProjectPrices::route('/'),
            'create' => Pages\CreateItemProjectPrice::route('/create'),
            'edit' => Pages\EditItemProjectPrice::route('/{record}/edit'),
        ];
    }
}
