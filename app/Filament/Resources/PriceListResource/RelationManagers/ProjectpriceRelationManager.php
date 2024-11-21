<?php

namespace App\Filament\Resources\PriceListResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Actions\ImportAction;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Imports\ItemProjectPriceImporter;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class ProjectpriceRelationManager extends RelationManager
{
    protected static string $relationship = 'projectprice';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('item_code')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('item_code')
            
            ->columns([
                Tables\Columns\TextColumn::make('item_code')->searchable(),
                Tables\Columns\TextColumn::make('projectlist.name')->label('Project'),
                Tables\Columns\TextColumn::make('price')->searchable()->money()->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                ImportAction::make()
                ->importer(ItemProjectPriceImporter::class)->label('Import')->tooltip('Update or Create Project Prices'),
            
              
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
