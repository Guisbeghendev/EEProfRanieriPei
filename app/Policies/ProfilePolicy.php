<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Profile;

class ProfilePolicy
{
    public function view(User $user, Profile $profile)
    {
        return true; // perfis são públicos
    }

    public function update(User $user, Profile $profile)
    {
        return $user->hasRole('admin') || $user->id === $profile->user_id;
    }
}
