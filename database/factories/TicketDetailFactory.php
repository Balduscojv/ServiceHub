<?php

namespace Database\Factories;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketDetailFactory extends Factory
{
    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'technical_notes' => fake()->paragraph(),
            'additional_data' => null,
            'processed_at' => null,
        ];
    }

    public function processed(): static
    {
        return $this->state(fn () => [
            'additional_data' => ['key' => 'value', 'processed' => true],
            'processed_at' => now(),
        ]);
    }
}
