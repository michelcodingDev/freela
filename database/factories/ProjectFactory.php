<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence(3);

        return [
            'client_id' => User::factory()->cliente(),
            'category_id' => Category::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->uuid(),
            'description' => fake()->paragraph(3),
            'experience_level' => fake()->randomElement(['iniciante', 'intermediario', 'especialista']),
            'visibility' => 'publico',
            'status' => 'aguardando_propostas',
            'proposal_deadline' => now()->addDays(7),
            'budget_min' => 100,
            'budget_max' => 1000,
            'is_featured' => false,
            'featured_fee' => 0,
        ];
    }
}
