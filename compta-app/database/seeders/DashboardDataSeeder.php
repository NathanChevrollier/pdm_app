<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vehicule;
use App\Models\Commande;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class DashboardDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('fr_FR');
        // Désactiver les événements pour accélérer le seed
        User::flushEventListeners();
        Vehicule::flushEventListeners();
        Commande::flushEventListeners();
        
        // Créer 10 employés
        $employes = User::factory(10)->state([
            'statut' => function() {
                $statuts = ['Patron', 'Co-patron', 'Manager', 'Vendeur', 'Recrue'];
                return $statuts[array_rand($statuts)];
            },
            'prenom' => fn() => $faker->firstName
        ])->create();
        
        // Créer 20 véhicules
        $vehicules = Vehicule::factory(20)->create();
        
        // Créer 50 commandes
        for ($i = 0; $i < 50; $i++) {
            $date = now()->subDays(rand(0, 365));
            
            Commande::create([
                'nom_client' => $faker->name,
                'user_id' => $employes->random()->id,
                'vehicule_id' => $vehicules->random()->id,
                'date_commande' => $date,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }
    }
    
    /**
     * Retourne un statut aléatoire
     * 
     * @return string
     */
    private function getRandomStatus(): string
    {
        $statuses = ['pending', 'processing', 'completed', 'cancelled'];
        return $statuses[array_rand($statuses)];
    }
}
