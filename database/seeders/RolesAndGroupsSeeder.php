<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Group;
use App\Models\Role;

class RolesAndGroupsSeeder extends Seeder
{
    public function run(): void
    {
        // Cria o grupo "público" padrão
        Group::firstOrCreate([
            'name' => 'público',
        ], [
            'description' => 'Grupo padrão para novos usuários',
        ]);

        // Lista de roles (funções dos usuários)
        $roles = [
            'admin',    // gestão total
            'fotografo',        // upload e organização de galerias
            'público',          // visitante não autenticado
            'família',
            'aluno',
            'professor',
            'funcionário',
            'gestão',
            'DE',               // Diretoria de Ensino
        ];

        // Cria cada role se ainda não existir
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }
    }
}
