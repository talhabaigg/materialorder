<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Project;
use Filament\Forms\Form;
use App\Models\PriceList;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PriceListResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PriceListResource\RelationManagers;
use App\Filament\Resources\PriceListResource\RelationManagers\ProjectpriceRelationManager;

class PriceListResource extends Resource
{
    protected static ?string $model = PriceList::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
    protected static ?string $navigationGroup = 'Admin';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('name')
                    ->required()
                    ->columnSpanFull(),

              Select::make('site_reference')
                    ->label('Select Project') // Optional: add a label
                    ->options(function () {
                        return Project::all()->pluck('name', 'site_reference')->toArray();
                    })
                    ->placeholder('Select a Project')->columnSpanFull() // Optional: add a placeholder
                , 
 
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('project.name')
                    ->badge()
                    ->sortable(),
                
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            ProjectpriceRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPriceLists::route('/'),
            'create' => Pages\CreatePriceList::route('/create'),
            'edit' => Pages\EditPriceList::route('/{record}/edit'),
            'view' => Pages\ViewPriceList::route('/{record}/view'),
        ];
    }
}
