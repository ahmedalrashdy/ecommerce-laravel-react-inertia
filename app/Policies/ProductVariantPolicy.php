<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ProductVariant;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductVariantPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ProductVariant');
    }

    public function view(AuthUser $authUser, ProductVariant $productVariant): bool
    {
        return $authUser->can('View:ProductVariant');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProductVariant');
    }

    public function update(AuthUser $authUser, ProductVariant $productVariant): bool
    {
        return $authUser->can('Update:ProductVariant');
    }

    public function delete(AuthUser $authUser, ProductVariant $productVariant): bool
    {
        return $authUser->can('Delete:ProductVariant');
    }

    public function replicate(AuthUser $authUser, ProductVariant $productVariant): bool
    {
        return $authUser->can('Replicate:ProductVariant');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ProductVariant');
    }

}