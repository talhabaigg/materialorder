<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Hash;
use Filament\Tables\Columns\ImageColumn;
use LaraZeus\Popover\Tables\PopoverColumn;
use App\Filament\Resources\UserResource\Pages;

class UserResource extends Resource
{
    /**
     * The resource model.
     */
    protected static ?string $model = User::class;

    /**
     * The resource navigation icon.
     */
    protected static ?string $navigationIcon = 'heroicon-o-users';

    /**
     * The settings navigation group.
     */
    protected static ?string $navigationGroup = 'Main';

    /**
     * The settings navigation sort order.
     */
    protected static ?int $navigationSort = 1;

    /**
     * Get the navigation badge for the resource.
     */
    public static function getNavigationBadge(): ?string
    {
        return number_format(static::getModel()::count());
    }

    /**
     * The resource form.
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                Forms\Components\TextInput::make('password')
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->password()
                    ->confirmed()
                    ->maxLength(255),

                Forms\Components\TextInput::make('password_confirmation')
                    ->label('Confirm password')
                    ->password()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->maxLength(255),
                Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
            ]);
    }

    /**
     * The resource table.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                PopoverColumn::make('user_detail')
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
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('email')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')  // Adjust based on how the role is stored
                    ->label('Role')
                    ->searchable()
                    ->badge(),
  
           ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    /**
     * The resource relation managers.
     */
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /**
     * The resource pages.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
        ];
    }
}
