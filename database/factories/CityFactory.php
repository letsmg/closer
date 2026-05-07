<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;

class CityFactory extends Factory
{
    protected $model = City::class;

    public function definition()
    {
        return [
            'state_id' => State::factory(),
            'name' => $this->faker->unique()->city,
            'display_name' => $this->faker->city,
            'country_code' => 'BR',
            'geoname_id' => $this->faker->unique()->numberBetween(1000000, 9999999),
            'latitude' => $this->faker->latitude(-33.0, -23.0),
            'longitude' => $this->faker->longitude(-73.0, -33.0),
        ];
    }

    /**
     * Create an active city
     */
    public function active()
    {
        return $this->state(fn (array $attributes) => [
            'active' => true,
        ]);
    }

    /**
     * Create an inactive city
     */
    public function inactive()
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }

    /**
     * Create a city for a specific state
     */
    public function forState(State $state)
    {
        return $this->state(fn (array $attributes) => [
            'state_id' => $state->id,
        ]);
    }

    /**
     * Create São Paulo (SP)
     */
    public function saoPaulo()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'São Paulo',
            'active' => true,
        ]);
    }

    /**
     * Create Rio de Janeiro (RJ)
     */
    public function rioDeJaneiro()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Rio de Janeiro',
            'active' => true,
        ]);
    }

    /**
     * Create Belo Horizonte (MG)
     */
    public function beloHorizonte()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Belo Horizonte',
            'active' => true,
        ]);
    }

    /**
     * Create Salvador (BA)
     */
    public function salvador()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Salvador',
            'active' => true,
        ]);
    }

    /**
     * Create Brasília (DF)
     */
    public function brasilia()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Brasília',
            'active' => true,
        ]);
    }

    /**
     * Create Fortaleza (CE)
     */
    public function fortaleza()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Fortaleza',
            'active' => true,
        ]);
    }

    /**
     * Create Curitiba (PR)
     */
    public function curitiba()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Curitiba',
            'active' => true,
        ]);
    }

    /**
     * Create Recife (PE)
     */
    public function recife()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Recife',
            'active' => true,
        ]);
    }

    /**
     * Create Porto Alegre (RS)
     */
    public function portoAlegre()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Porto Alegre',
            'active' => true,
        ]);
    }

    /**
     * Create Campinas (SP)
     */
    public function campinas()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Campinas',
            'active' => true,
        ]);
    }

    /**
     * Create Guarulhos (SP)
     */
    public function guarulhos()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Guarulhos',
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
            'active' => true,
        ]);
    }

    /**
     * Create Los Angeles (USA)
     */
    public function losAngeles()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Los Angeles',
            'active' => true,
        ]);
    }

    /**
     * Create Chicago (USA)
     */
    public function chicago()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Chicago',
            'active' => true,
        ]);
    }

    /**
     * Create Miami (USA)
     */
    public function miami()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Miami',
            'active' => true,
        ]);
    }

    /**
     * Create Toronto (Canada)
     */
    public function toronto()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Toronto',
            'active' => true,
        ]);
    }

    /**
     * Create Montreal (Canada)
     */
    public function montreal()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Montreal',
            'active' => true,
        ]);
    }

    /**
     * Create Vancouver (Canada)
     */
    public function vancouver()
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Vancouver',
            'active' => true,
        ]);
    }

    /**
     * Create with specific name
     */
    public function name(string $name)
    {
        return $this->state(fn (array $attributes) => [
            'name' => $name,
        ]);
    }

    /**
     * Create for São Paulo state
     */
    public function forSaoPauloState()
    {
        return $this->forState(State::factory()->saoPaulo());
    }

    /**
     * Create for Rio de Janeiro state
     */
    public function forRioDeJaneiroState()
    {
        return $this->forState(State::factory()->rioDeJaneiro());
    }

    /**
     * Create for Minas Gerais state
     */
    public function forMinasGeraisState()
    {
        return $this->forState(State::factory()->minasGerais());
    }

    /**
     * Create for California state
     */
    public function forCaliforniaState()
    {
        return $this->forState(State::factory()->california());
    }

    /**
     * Create for Texas state
     */
    public function forTexasState()
    {
        return $this->forState(State::factory()->texas());
    }

    /**
     * Create for New York state
     */
    public function forNewYorkState()
    {
        return $this->forState(State::factory()->newYork());
    }

    /**
     * Create for Ontario state
     */
    public function forOntarioState()
    {
        return $this->forState(State::factory()->ontario());
    }

    /**
     * Create for Quebec state
     */
    public function forQuebecState()
    {
        return $this->forState(State::factory()->quebec());
    }
}
