<?php

namespace Database\Factories;

use App\Models\Profile;
use App\Models\User;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'nickname' => $this->faker->userName,
            'birth_date' => $this->faker->dateTimeBetween('-60 years', '-18 years'),
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            'gender_identity' => $this->faker->randomElement(['cisgender', 'transgender', 'non-binary', 'genderqueer']),
            'sexual_orientation' => $this->faker->randomElement(['heterosexual', 'homosexual', 'bisexual', 'pansexual', 'asexual']),
            'purpose' => $this->faker->randomElement(['relationship', 'friendship', 'dating', 'casual', 'marriage']),
            'profession' => $this->faker->jobTitle,
            'biography' => $this->faker->text(200),
            'smoker' => $this->faker->randomElement(['no', 'occasionally', 'regularly', 'trying_to_quit']),
            'drinker' => $this->faker->randomElement(['no', 'occasionally', 'regularly', 'socially']),
            'marital_status' => $this->faker->randomElement(['single', 'married', 'divorced', 'widowed', 'open_relationship']),
            'country_id' => Country::factory(),
            'state_id' => State::factory(),
            'city_id' => City::factory(),
            'visibility' => $this->faker->randomElement(['public', 'hidden', 'matches_only']),
            'latitude' => $this->faker->latitude(-23.0, -33.0), // Brazil approximate range
            'longitude' => $this->faker->longitude(-73.0, -33.0), // Brazil approximate range
        ];
    }

    /**
     * Create a profile for a specific user
     */
    public function forUser(User $user)
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Create a public profile
     */
    public function public()
    {
        return $this->state(fn (array $attributes) => [
            'visibility' => 'public',
        ]);
    }

    /**
     * Create a hidden profile
     */
    public function hidden()
    {
        return $this->state(fn (array $attributes) => [
            'visibility' => 'hidden',
        ]);
    }

    /**
     * Create a profile with specific gender
     */
    public function gender(string $gender)
    {
        return $this->state(fn (array $attributes) => [
            'gender' => $gender,
        ]);
    }

    /**
     * Create a profile with specific location
     */
    public function location(Country $country, State $state, City $city)
    {
        return $this->state(fn (array $attributes) => [
            'country_id' => $country->id,
            'state_id' => $state->id,
            'city_id' => $city->id,
        ]);
    }

    /**
     * Create a complete profile with all fields filled
     */
    public function complete()
    {
        return $this->state(fn (array $attributes) => [
            'gender_identity' => $this->faker->randomElement(['cisgender', 'transgender', 'non-binary', 'genderqueer']),
            'sexual_orientation' => $this->faker->randomElement(['heterosexual', 'homosexual', 'bisexual', 'pansexual', 'asexual']),
            'biography' => $this->faker->text(500),
        ]);
    }
}
