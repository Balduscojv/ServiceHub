<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['active', 'inactive', 'completed']),
        ];
    }
}
