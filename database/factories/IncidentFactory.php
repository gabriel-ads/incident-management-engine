<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Incident>
 */
class IncidentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => "Pishings a todo momento",
            'evidence' => "Nosso e-mail corporativo chega pishing a todo momento",
            'criticality' => 4,
            'host' => "ambev.tech.br",
            'user_id' => 5
        ];
    }
}
