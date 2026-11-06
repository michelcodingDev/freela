<?php

namespace Database\Factories;

use App\Models\Plan;
use App\Models\User;
use App\Models\UserPlanSubscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserPlanSubscription>
 */
class UserPlanSubscriptionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'plan_id' => Plan::factory(),
            'status' => 'ativo',
            'starts_at' => now()->subMonth(),
            'ends_at' => now()->addMonth(),
            'price_paid' => 49.90,
        ];
    }
}
