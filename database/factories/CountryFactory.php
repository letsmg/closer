<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

class CountryFactory extends Factory
{
    protected $model = Country::class;

    public function definition()
    {
        return [
            'code' => $this->faker->unique()->countryCode,
            'abbreviation' => $this->faker->unique()->countryCode,
        ];
    }

    /**
     * Create an active country
     */
    public function active()
    {
        return $this->state(fn (array $attributes) => [
            'active' => true,
        ]);
    }

    /**
     * Create an inactive country
     */
    public function inactive()
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }

    /**
     * Create Brazil
     */
    public function brazil()
    {
        return $this->state(fn (array $attributes) => [
            'code' => 'BR',
            'abbreviation' => 'BR',
            'active' => true,
        ]);
    }

    /**
     * Create United States
     */
    public function unitedStates()
    {
        return $this->state(fn (array $attributes) => [
            'code' => 'US',
            'abbreviation' => 'US',
            'active' => true,
        ]);
    }

    /**
     * Create Portugal
     */
    public function portugal()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Portugal',
            'code' => 'PT',
            'active' => true,
        ]);
    }

    /**
     * Create Spain
     */
    public function spain()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Spain',
            'code' => 'ES',
            'active' => true,
        ]);
    }

    /**
     * Create Argentina
     */
    public function argentina()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Argentina',
            'code' => 'AR',
            'active' => true,
        ]);
    }

    /**
     * Create Mexico
     */
    public function mexico()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Mexico',
            'code' => 'MX',
            'active' => true,
        ]);
    }

    /**
     * Create Canada
     */
    public function canada()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Canada',
            'code' => 'CA',
            'active' => true,
        ]);
    }

    /**
     * Create United Kingdom
     */
    public function unitedKingdom()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'United Kingdom',
            'code' => 'GB',
            'active' => true,
        ]);
    }

    /**
     * Create France
     */
    public function france()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'France',
            'code' => 'FR',
            'active' => true,
        ]);
    }

    /**
     * Create Germany
     */
    public function germany()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Germany',
            'code' => 'DE',
            'active' => true,
        ]);
    }

    /**
     * Create Italy
     */
    public function italy()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Italy',
            'code' => 'IT',
            'active' => true,
        ]);
    }

    /**
     * Create Japan
     */
    public function japan()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Japan',
            'code' => 'JP',
            'active' => true,
        ]);
    }

    /**
     * Create China
     */
    public function china()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'China',
            'code' => 'CN',
            'active' => true,
        ]);
    }

    /**
     * Create Australia
     */
    public function australia()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Australia',
            'code' => 'AU',
            'active' => true,
        ]);
    }

    /**
     * Create India
     */
    public function india()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'India',
            'code' => 'IN',
            'active' => true,
        ]);
    }

    /**
     * Create with specific name and code
     */
    public function withNameAndCode(string $name, string $code)
    {
        return $this->state(fn (array $attributes) => [
            'name' => $name,
            'code' => $code,
        ]);
    }
}
