<?php

namespace Database\Factories;

use App\Models\Dispute;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Dispute>
 */
class DisputeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'client_id' => User::factory()->cliente(),
            'freelancer_id' => User::factory()->freelancer(),
            'reason' => fake()->sentence(10),
            'status' => 'aberta',
        ];
    }
}
