<?php

namespace Database\Factories;

use App\Models\VisitorLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VisitorLog>
 */
class VisitorLogFactory extends Factory
{
    protected $model = VisitorLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'country' => $this->faker->country(),
            'region' => $this->faker->state(),
            'city' => $this->faker->city(),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'page_url' => $this->faker->url(),
            'referrer_url' => $this->faker->url(),
            'cookies_consented' => $this->faker->boolean(70),
        ];
    }
}
