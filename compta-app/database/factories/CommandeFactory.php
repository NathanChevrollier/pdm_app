<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Commande>
 */
class CommandeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = $this->faker->dateTimeBetween('-1 year', 'now');
        
        return [
            'nom_client' => $this->faker->name,
            'user_id' => \App\Models\User::factory(),
            'vehicule_id' => \App\Models\Vehicule::factory(),
            'date_commande' => $date,
            'created_at' => $date,
            'updated_at' => $date,
        ];
    }
}
