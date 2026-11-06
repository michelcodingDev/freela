<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectInvite;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectInvite>
 */
class ProjectInviteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'client_id' => User::factory()->cliente(),
            'freelancer_id' => User::factory()->freelancer(),
            'status' => 'convidado',
        ];
    }
}
