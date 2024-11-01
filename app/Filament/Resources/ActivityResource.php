<?php

namespace App\Filament\Resources;

use Illuminate\Support\Facades\Auth;
use Z3d0X\FilamentLogger\Resources\ActivityResource as BaseResource;

class ActivityResource extends BaseResource
{
    /**
     * The resource navigation sort order.
     */
    protected static ?int $navigationSort = 1;

    /**
     * Get the navigation badge for the resource.
     */
    public static function getNavigationBadge(): ?string
    {
        return number_format(static::getModel()::count());
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();

        // Check if the user has the 'super_admin' role
        return $user && $user->hasRole('super_admin');
    }
}
