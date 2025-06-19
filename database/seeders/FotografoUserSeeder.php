<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Group;
use Illuminate\Support\Facades\Hash;

class FotografoUserSeeder extends Seeder // <- NOME DA CLASSE AGORA É FotografoUserSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Garante que a role "fotografo" exista
        $fotografoRole = Role::firstOrCreate(['name' => 'fotografo']);

        // Garante que o grupo "fotografo" exista
        $fotografoGroup = Group::firstOrCreate(
            ['name' => 'fotografo'],
            ['description' => 'Grupo de usuários fotografos']
        );

        // Cria ou busca o usuário fotógrafo
        $fotografo = User::updateOrCreate(
            ['email' => 'fotografo.ranieri@gmail.com'],
            [
                'name' => 'Fotógrafo',
                'password' => Hash::make('Gsp@ranieri2025'), // Use uma senha segura
                'email_verified_at' => now(),
            ]
        );

        // Associa a role 'fotografo' ao usuário
        if (!$fotografo->roles->contains($fotografoRole->id)) {
            $fotografo->roles()->attach($fotografoRole->id);
        }

        // Associa o grupo 'fotografo' ao usuário
        if (!$fotografo->groups->contains($fotografoGroup->id)) {
            $fotografo->groups()->attach($fotografoGroup->id);
        }
    }
}
