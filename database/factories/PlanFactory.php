<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Plan>
 */
class PlanFactory extends Factory
{
    public function definition(): array
    {
        $slug = fake()->unique()->slug();

        return [
            'name' => fake()->word(),
            'slug' => 'plan-'.$slug,
            'description' => fake()->sentence(),
            'price_monthly' => fake()->randomFloat(2, 0, 100),
            'commission_percentage' => fake()->randomFloat(2, 5, 20),
            'min_commission' => 5,
            'proposals_per_month' => fake()->randomElement([10, 50, 9999]),
            'max_portfolio_items' => fake()->randomElement([3, 12, 15]),
            'withdrawal_days' => fake()->randomElement([2, 4, 6]),
            'is_featured' => false,
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 10),
        ];
    }
}
