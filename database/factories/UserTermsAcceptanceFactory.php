<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserTermsAcceptance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserTermsAcceptance>
 */
class UserTermsAcceptanceFactory extends Factory
{
    protected $model = UserTermsAcceptance::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'terms_version' => '2026-05-20',
            'privacy_version' => '2026-05-20',
            'accepted_at' => now(),
            'ip_address' => $this->faker->ipv4(),
        ];
    }
}
