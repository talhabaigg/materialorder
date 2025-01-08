<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ItemBaseResource\Pages;
use App\Filament\Resources\ItemBaseResource\RelationManagers;
use App\Filament\Resources\ItemBaseResource\RelationManagers\PriceRelationManager;
use App\Models\ItemBase;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItemBaseResource extends Resource
{
    protected static ?string $model = ItemBase::class;
    protected static ?string $navigationLabel = 'Manage Base Prices';
    protected static ?string $navigationGroup = 'Admin';
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('name')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\DatePicker::make('effective_from')
                    ->required(),
                Forms\Components\DatePicker::make('effective_to'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('effective_from')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('effective_to')
                    ->date()
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
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PriceRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListItemBases::route('/'),
            'create' => Pages\CreateItemBase::route('/create'),
            'edit' => Pages\EditItemBase::route('/{record}/edit'),
        ];
    }
}
