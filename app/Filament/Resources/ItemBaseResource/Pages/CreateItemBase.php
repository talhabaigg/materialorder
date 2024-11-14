<?php

namespace App\Filament\Resources\ItemBaseResource\Pages;

use App\Filament\Resources\ItemBaseResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateItemBase extends CreateRecord
{
    protected static string $resource = ItemBaseResource::class;
}
