<?php

namespace App\Filament\Resources\Roles;

use App\Enums\NavigationGroup;
use BezhanSalleh\FilamentShield\Resources\Roles\RoleResource as ShieldRoleResource;
use UnitEnum;

class RoleResource extends ShieldRoleResource
{
    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::UsersAndPermissions;

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('shield.resource.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('shield.resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('shield.resource.plural_model_label');
    }
}
