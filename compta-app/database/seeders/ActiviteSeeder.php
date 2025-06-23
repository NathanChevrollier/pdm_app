<?php

namespace Database\Seeders;

use App\Models\Activite;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ActiviteSeeder extends Seeder
{
    /**
     * Exécute le seeder.
     */
    public function run(): void
    {
        // Vérifier si un utilisateur existe, sinon en créer un
        $user = User::first();
        
        if (!$user) {
            $user = User::create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
        }

        // Données d'exemple pour les activités
        $activites = [
            [
                'titre' => 'Nouveau client enregistré',
                'description' => 'Un nouveau client a été ajouté au système.',
                'type' => 'success',
                'icon' => 'bx-user-plus',
                'color' => 'success',
                'user_id' => $user->id,
                'created_at' => now()->subMinutes(5),
            ],
            [
                'titre' => 'Nouvelle commande',
                'description' => 'Une nouvelle commande a été passée.',
                'type' => 'info',
                'icon' => 'bx-cart',
                'color' => 'primary',
                'user_id' => $user->id,
                'created_at' => now()->subHours(2),
            ],
            [
                'titre' => 'Paiement reçu',
                'description' => 'Un paiement de 125 000 FCFA a été enregistré.',
                'type' => 'success',
                'icon' => 'bx-check-shield',
                'color' => 'success',
                'user_id' => $user->id,
                'created_at' => now()->subHours(3),
                'metadata' => [
                    'montant' => 125000,
                    'devise' => 'FCFA',
                    'reference' => 'PAY-' . Str::random(8),
                ],
            ],
            [
                'titre' => 'Véhicule ajouté',
                'description' => 'Un nouveau véhicule a été ajouté au parc.',
                'type' => 'info',
                'icon' => 'bx-car',
                'color' => 'info',
                'user_id' => $user->id,
                'created_at' => now()->subDay(),
            ],
            [
                'titre' => 'Maintenance préventive',
                'description' => 'La maintenance préventive du véhicule #123 a été planifiée.',
                'type' => 'warning',
                'icon' => 'bx-wrench',
                'color' => 'warning',
                'user_id' => $user->id,
                'created_at' => now()->subDays(2),
            ],
        ];

        // Insérer les activités dans la base de données
        foreach ($activites as $activite) {
            Activite::create($activite);
        }

        $this->command->info('Activités de démo créées avec succès!');
    }
}
