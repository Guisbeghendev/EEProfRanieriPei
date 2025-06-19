<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Group;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Garante que a role "admin" exista
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Garante que o grupo "admin" exista
        $adminGroup = Group::firstOrCreate([
            'name' => 'admin',
        ], [
            'description' => 'Grupo de administradores do sistema',
        ]);

        // Cria ou busca o usuário admin
        // Usamos updateOrCreate para garantir que, se o e-mail já existir, apenas os outros campos sejam atualizados.
        // Isso ajuda a evitar problemas se o seeder for rodado várias vezes sem fresh.
        $admin = User::updateOrCreate(
            ['email' => 'admin.ranieri@gmail.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('Gsp@ranieri2025'),
                'email_verified_at' => now(), // Define email_verified_at para simular um usuário verificado
            ]
        );

        // Associa a role 'admin' ao usuário, garantindo que não haja duplicação
        // Usamos attach() para garantir a associação se ela não existir.
        // O syncWithoutDetaching() é bom, mas o attach() é mais explícito para 'garantir a adição'.
        if (!$admin->roles->contains($adminRole->id)) {
            $admin->roles()->attach($adminRole->id);
        }

        // Associa o grupo 'admin' ao usuário
        if (!$admin->groups->contains($adminGroup->id)) {
            $admin->groups()->attach($adminGroup->id);
        }
    }
}
