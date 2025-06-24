<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            DashboardDataSeeder::class,
            ActiviteSeeder::class,
            ObjectifSeeder::class,
            // Ajoutez d'autres seeders ici si nécessaire
        ]);
    }
}
