<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Withdrawal>
 */
class WithdrawalFactory extends Factory
{
    public function definition(): array
    {
        return [
            'freelancer_id' => User::factory()->freelancer(),
            'amount' => 500,
            'fee' => 10,
            'net_amount' => 490,
            'pix_key' => 'pix@example.com',
            'status' => 'solicitado',
        ];
    }
}
