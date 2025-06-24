<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un utilisateur administrateur
        User::create([
            'nom' => 'Admin',
            'prenom' => 'Système',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'statut' => 'admin',
            'commission' => 0,
        ]);

        // Créer un utilisateur employé de test
        User::create([
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'email' => 'employe@example.com',
            'password' => Hash::make('password'),
            'statut' => 'employe',
            'commission' => 5.00, // 5% de commission
        ]);
    }
}
