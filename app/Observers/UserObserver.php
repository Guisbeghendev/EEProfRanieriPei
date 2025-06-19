<?php

// app/Observers/UserObserver.php

namespace App\Observers;

use App\Models\User;
use App\Models\Group;
use App\Models\Role;

class UserObserver
{
    public function created(User $user): void
    {
        // Cria perfil vazio
        $user->profile()->create();

        // Cria ou pega role público e associa ao usuário
        $role = Role::firstOrCreate(['name' => 'público']);
        $user->roles()->syncWithoutDetaching([$role->id]);

        // Cria ou pega grupo público e associa ao usuário
        $group = Group::firstOrCreate(['name' => 'público']);
        $user->groups()->syncWithoutDetaching([$group->id]);
    }
}
