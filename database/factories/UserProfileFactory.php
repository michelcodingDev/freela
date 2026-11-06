<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserProfile>
 */
class UserProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'phone' => fake()->phoneNumber(),
            'city' => fake()->city(),
            'state' => fake()->stateAbbr(),
            'zip_code' => fake()->postcode(),
            'bio' => fake()->paragraph(),
            'professional_title' => fake()->jobTitle(),
            'pix_key' => fake()->email(),
            'rating_average' => 0,
            'total_reviews' => 0,
            'total_earned' => 0,
            'projects_completed' => 0,
        ];
    }
}
