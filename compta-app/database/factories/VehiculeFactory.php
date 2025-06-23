<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicule>
 */
class VehiculeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $marques = ['Toyota', 'Honda', 'Ford', 'BMW', 'Mercedes', 'Audi', 'Nissan', 'Hyundai', 'Kia', 'Mazda'];
        $modeles = [
            'Toyota' => ['Corolla', 'Camry', 'RAV4', 'Hilux', 'Prado'],
            'Honda' => ['Civic', 'Accord', 'CR-V', 'HR-V', 'Pilot'],
            'Ford' => ['Fiesta', 'Focus', 'Mustang', 'Explorer', 'Ranger'],
            'BMW' => ['Série 3', 'Série 5', 'X3', 'X5', 'i8'],
            'Mercedes' => ['Classe A', 'Classe C', 'GLA', 'GLC', 'Classe S'],
            'Audi' => ['A3', 'A4', 'A6', 'Q3', 'Q5'],
            'Nissan' => ['Sunny', 'X-Trail', 'Qashqai', 'Patrol', 'Navara'],
            'Hyundai' => ['i10', 'i20', 'Tucson', 'Santa Fe', 'Creta'],
            'Kia' => ['Picanto', 'Rio', 'Sportage', 'Sorento', 'Seltos'],
            'Mazda' => ['2', '3', 'CX-3', 'CX-5', 'CX-9'],
        ];
        
        $marque = $this->faker->randomElement($marques);
        $modele = $this->faker->randomElement($modeles[$marque]);
        $prixAchat = $this->faker->numberBetween(5000000, 50000000);
        
        return [
            'nom' => "$marque $modele",
            'prix_achat' => $prixAchat,
            'prix_vente' => $prixAchat * 1.2, // 20% de marge
        ];
    }
}
