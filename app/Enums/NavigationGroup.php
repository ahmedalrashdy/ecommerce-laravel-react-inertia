<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum NavigationGroup implements HasLabel
{
    case Catalog;

    case Customers;

    case Operations;

    case OrdersManagement;

    case ReturnsManagement;

    case UsersAndPermissions;

    case Settings;

    public function getLabel(): string
    {
        return match ($this) {
            self::Catalog => __('filament.navigation-group.catelog'),
            self::Customers => __('filament.navigation-group.customers'),
            self::Operations => __('filament.navigation-group.operations'),
            self::OrdersManagement => __('filament.navigation-group.orders_management'),
            self::ReturnsManagement => __('filament.navigation-group.returns_management'),
            self::UsersAndPermissions => __('filament.navigation-group.users_and_permissions'),
            self::Settings => __('filament.navigation-group.settings'),
        };
    }
}
