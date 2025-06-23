<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employe>
 */
class EmployeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' => $this->faker->name,
            'statut' => $this->faker->randomElement(['admin', 'employe', 'manager', 'vendeur', 'comptable', 'rh']),
            'commission' => $this->faker->randomFloat(2, 0, 100), // Commission entre 0 et 100%
        ];
    }
}
