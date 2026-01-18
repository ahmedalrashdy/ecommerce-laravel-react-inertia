<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Cart;
use Illuminate\Auth\Access\HandlesAuthorization;

class CartPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Cart');
    }

    public function view(AuthUser $authUser, Cart $cart): bool
    {
        return $authUser->can('View:Cart');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Cart');
    }

    public function update(AuthUser $authUser, Cart $cart): bool
    {
        return $authUser->can('Update:Cart');
    }

    public function delete(AuthUser $authUser, Cart $cart): bool
    {
        return $authUser->can('Delete:Cart');
    }

    public function replicate(AuthUser $authUser, Cart $cart): bool
    {
        return $authUser->can('Replicate:Cart');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Cart');
    }

}