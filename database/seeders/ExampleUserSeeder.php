<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ExampleUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate([
            'email' => 'teste1@gmail.com',
        ], [
            'name' => 'Teste1',
            'password' => Hash::make('Gsp@teste2025'),
        ]);

        // O observer cuida de perfil, role e grupo
    }
}
