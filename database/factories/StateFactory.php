<?php

namespace Database\Factories;

use App\Models\State;
use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

class StateFactory extends Factory
{
    protected $model = State::class;

    public function definition()
    {
        return [
            'country_id' => Country::factory(),
            'name' => $this->faker->unique()->state,
            'uf' => $this->faker->unique()->stateAbbr,
        ];
    }

    /**
     * Create an active state
     */
    public function active()
    {
        return $this->state(fn (array $attributes) => [
            'active' => true,
        ]);
    }

    /**
     * Create an inactive state
     */
    public function inactive()
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }

    /**
     * Create a state for a specific country
     */
    public function forCountry(Country $country)
    {
        return $this->state(fn (array $attributes) => [
            'country_id' => $country->id,
        ]);
    }

    /**
     * Create São Paulo (Brazil)
     */
    public function saoPaulo()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'São Paulo',
            'code' => 'SP',
            'active' => true,
        ]);
    }

    /**
     * Create Rio de Janeiro (Brazil)
     */
    public function rioDeJaneiro()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Rio de Janeiro',
            'code' => 'RJ',
            'active' => true,
        ]);
    }

    /**
     * Create Minas Gerais (Brazil)
     */
    public function minasGerais()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Minas Gerais',
            'code' => 'MG',
            'active' => true,
        ]);
    }

    /**
     * Create Bahia (Brazil)
     */
    public function bahia()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Bahia',
            'code' => 'BA',
            'active' => true,
        ]);
    }

    /**
     * Create Paraná (Brazil)
     */
    public function parana()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Paraná',
            'code' => 'PR',
            'active' => true,
        ]);
    }

    /**
     * Create Rio Grande do Sul (Brazil)
     */
    public function rioGrandeDoSul()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Rio Grande do Sul',
            'code' => 'RS',
            'active' => true,
        ]);
    }

    /**
     * Create Pernambuco (Brazil)
     */
    public function pernambuco()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Pernambuco',
            'code' => 'PE',
            'active' => true,
        ]);
    }

    /**
     * Create Ceará (Brazil)
     */
    public function ceara()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Ceará',
            'code' => 'CE',
            'active' => true,
        ]);
    }

    /**
     * Create California (USA)
     */
    public function california()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'California',
            'code' => 'CA',
            'active' => true,
        ]);
    }

    /**
     * Create Texas (USA)
     */
    public function texas()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Texas',
            'code' => 'TX',
            'active' => true,
        ]);
    }

    /**
     * Create New York (USA)
     */
    public function newYork()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'New York',
            'code' => 'NY',
            'active' => true,
        ]);
    }

    /**
     * Create Florida (USA)
     */
    public function florida()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Florida',
            'code' => 'FL',
            'active' => true,
        ]);
    }

    /**
     * Create Ontario (Canada)
     */
    public function ontario()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Ontario',
            'code' => 'ON',
            'active' => true,
        ]);
    }

    /**
     * Create Quebec (Canada)
     */
    public function quebec()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Quebec',
            'code' => 'QC',
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

    /**
     * Create for Brazil country
     */
    public function forBrazil()
    {
        return $this->forCountry(Country::factory()->brazil());
    }

    /**
     * Create for USA country
     */
    public function forUSA()
    {
        return $this->forCountry(Country::factory()->unitedStates());
    }

    /**
     * Create for Canada country
     */
    public function forCanada()
    {
        return $this->forCountry(Country::factory()->canada());
    }
}
