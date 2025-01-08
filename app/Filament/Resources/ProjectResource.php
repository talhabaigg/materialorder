<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Project;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProjectResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Filament\Resources\ProjectResource\RelationManagers\FavouriteMaterialsRelationManager;
use App\Filament\Resources\ProjectResource\RelationManagers\MaterialItemRelationManager;
use Filament\Tables\Actions\ViewAction;
use Tapp\FilamentGoogleAutocomplete\Forms\Components\GoogleAutocomplete;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;
    protected static ?string $navigationLabel = 'Projects';
    protected static ?string $navigationGroup = 'Admin';

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name'),

                TextInput::make('site_reference'),
                TextInput::make('delivery_contact'),
                TextInput::make('pickup_by'),
                TextInput::make('requested_by'),
                Textarea::make('notes')->columnSpanFull(),


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

                        Forms\Components\TextInput::make('coordinates')
                            ->extraInputAttributes([
                                'data-google-field' => '{latitude}, {longitude}',
                            ]),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('deliver_to'),
                TextColumn::make('coordinates'),
                TextColumn::make('site_reference'),
                TextColumn::make('delivery_contact'),

            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
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
            RelationManagers\UsersRelationManager::class,
            RelationManagers\MaterialsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
            'view' => Pages\ViewProject::route('/{record}/view'),
        ];
    }
}
