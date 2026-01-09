<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\UserAddress;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserAddressPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:UserAddress');
    }

    public function view(AuthUser $authUser, UserAddress $userAddress): bool
    {
        return $authUser->can('View:UserAddress');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:UserAddress');
    }

    public function update(AuthUser $authUser, UserAddress $userAddress): bool
    {
        return $authUser->can('Update:UserAddress');
    }

    public function delete(AuthUser $authUser, UserAddress $userAddress): bool
    {
        return $authUser->can('Delete:UserAddress');
    }

    public function replicate(AuthUser $authUser, UserAddress $userAddress): bool
    {
        return $authUser->can('Replicate:UserAddress');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:UserAddress');
    }

}