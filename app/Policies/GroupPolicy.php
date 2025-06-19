<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Group;

class GroupPolicy
{
    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Group $group)
    {
        return true;
    }

    public function create(User $user)
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, Group $group)
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, Group $group)
    {
        return $user->hasRole('admin');
    }
}
