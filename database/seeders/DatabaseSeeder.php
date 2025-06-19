<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndGroupsSeeder::class, // Se você usa este seeder
            ExampleUserSeeder::class,    // Se você usa este seeder
            AdminUserSeeder::class,
            FotografoUserSeeder::class,  // <- ADICIONE ESTA LINHA
        ]);
    }
}
