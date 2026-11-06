<?php

namespace Database\Factories;

use App\Models\PortfolioItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PortfolioItem>
 */
class PortfolioItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'freelancer_id' => User::factory()->freelancer(),
            'title' => fake()->sentence(3),
            'description' => fake()->sentence(),
            'is_public' => true,
        ];
    }
}
