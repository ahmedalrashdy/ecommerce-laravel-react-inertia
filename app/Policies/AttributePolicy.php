<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Attribute;
use Illuminate\Auth\Access\HandlesAuthorization;

class AttributePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Attribute');
    }

    public function view(AuthUser $authUser, Attribute $attribute): bool
    {
        return $authUser->can('View:Attribute');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Attribute');
    }

    public function update(AuthUser $authUser, Attribute $attribute): bool
    {
        return $authUser->can('Update:Attribute');
    }

    public function delete(AuthUser $authUser, Attribute $attribute): bool
    {
        return $authUser->can('Delete:Attribute');
    }

    public function replicate(AuthUser $authUser, Attribute $attribute): bool
    {
        return $authUser->can('Replicate:Attribute');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Attribute');
    }

}