<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'client_id' => User::factory()->cliente(),
            'freelancer_id' => User::factory()->freelancer(),
            'amount' => 1000,
            'platform_fee' => 200,
            'gateway_fee' => 20,
            'net_amount' => 780,
            'method' => 'pix',
            'status' => 'custodia',
            'payment_intent_id' => 'PIX_'.fake()->uuid(),
        ];
    }

    public function released(): static
    {
        return $this->state(fn () => ['status' => 'liberado', 'released_at' => now()]);
    }
}
