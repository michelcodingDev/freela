<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Proposal>
 */
class ProposalFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'freelancer_id' => User::factory()->freelancer(),
            'offer' => 500,
            'final_offer' => 500,
            'estimated_days' => 7,
            'details' => fake()->paragraph(2),
            'status' => 'pendente',
            'is_promoted' => false,
            'promoted_fee' => 0,
        ];
    }

    public function accepted(): static
    {
        return $this->state(fn () => ['status' => 'aceita']);
    }
}
