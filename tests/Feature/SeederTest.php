<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Hobby;
use App\Models\Language;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class SeederTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function country_seeder_creates_expected_countries()
    {
        Artisan::call('db:seed', ['--class' => 'CountrySeeder']);

        $this->assertDatabaseCount('countries', 250); // Expected number of countries

        // Check if specific countries exist
        $this->assertDatabaseHas('countries', [
            'name' => 'Brazil',
            'code' => 'BR',
            'active' => true,
        ]);

        $this->assertDatabaseHas('countries', [
            'name' => 'United States',
            'code' => 'US',
            'active' => true,
        ]);

        $this->assertDatabaseHas('countries', [
            'name' => 'Portugal',
            'code' => 'PT',
            'active' => true,
        ]);
    }

    /** @test */
    public function state_seeder_creates_expected_states()
    {
        // First seed countries
        Artisan::call('db:seed', ['--class' => 'CountrySeeder']);
        
        // Then seed states
        Artisan::call('db:seed', ['--class' => 'StateSeeder']);

        $this->assertDatabaseCount('states', 27); // Expected number of Brazilian states

        // Check if specific states exist
        $brazil = Country::where('code', 'BR')->first();
        
        $this->assertDatabaseHas('states', [
            'country_id' => $brazil->id,
            'name' => 'São Paulo',
            'code' => 'SP',
            'active' => true,
        ]);

        $this->assertDatabaseHas('states', [
            'country_id' => $brazil->id,
            'name' => 'Rio de Janeiro',
            'code' => 'RJ',
            'active' => true,
        ]);

        $this->assertDatabaseHas('states', [
            'country_id' => $brazil->id,
            'name' => 'Minas Gerais',
            'code' => 'MG',
            'active' => true,
        ]);
    }

    /** @test */
    public function city_seeder_creates_expected_cities()
    {
        // First seed countries and states
        Artisan::call('db:seed', ['--class' => 'CountrySeeder']);
        Artisan::call('db:seed', ['--class' => 'StateSeeder']);
        
        // Then seed cities
        Artisan::call('db:seed', ['--class' => 'CitySeeder']);

        $this->assertGreaterThan(5000, City::count()); // Expected minimum number of cities

        // Check if specific cities exist
        $spState = State::where('code', 'SP')->first();
        
        $this->assertDatabaseHas('cities', [
            'state_id' => $spState->id,
            'name' => 'São Paulo',
            'active' => true,
        ]);

        $this->assertDatabaseHas('cities', [
            'state_id' => $spState->id,
            'name' => 'Campinas',
            'active' => true,
        ]);

        $rjState = State::where('code', 'RJ')->first();
        $this->assertDatabaseHas('cities', [
            'state_id' => $rjState->id,
            'name' => 'Rio de Janeiro',
            'active' => true,
        ]);
    }

    /** @test */
    public function hobby_seeder_creates_expected_hobbies()
    {
        Artisan::call('db:seed', ['--class' => 'HobbySeeder']);

        $this->assertDatabaseCount('hobbies', 50); // Expected number of hobbies

        // Check if specific hobbies exist
        $this->assertDatabaseHas('hobbies', [
            'name' => 'Reading',
            'category' => 'intellectual',
            'active' => true,
        ]);

        $this->assertDatabaseHas('hobbies', [
            'name' => 'Sports',
            'category' => 'physical',
            'active' => true,
        ]);

        $this->assertDatabaseHas('hobbies', [
            'name' => 'Music',
            'category' => 'artistic',
            'active' => true,
        ]);

        $this->assertDatabaseHas('hobbies', [
            'name' => 'Travel',
            'category' => 'lifestyle',
            'active' => true,
        ]);
    }

    /** @test */
    public function language_seeder_creates_expected_languages()
    {
        Artisan::call('db:seed', ['--class' => 'LanguageSeeder']);

        $this->assertDatabaseCount('languages', 20); // Expected number of languages

        // Check if specific languages exist
        $this->assertDatabaseHas('languages', [
            'name' => 'English',
            'code' => 'en',
            'active' => true,
        ]);

        $this->assertDatabaseHas('languages', [
            'name' => 'Portuguese',
            'code' => 'pt',
            'active' => true,
        ]);

        $this->assertDatabaseHas('languages', [
            'name' => 'Spanish',
            'code' => 'es',
            'active' => true,
        ]);

        $this->assertDatabaseHas('languages', [
            'name' => 'French',
            'code' => 'fr',
            'active' => true,
        ]);
    }

    /** @test */
    public function user_seeder_creates_test_users()
    {
        Artisan::call('db:seed', ['--class' => 'UserSeeder']);

        $this->assertDatabaseCount('users', 10); // Expected number of test users

        // Check if admin user exists
        $this->assertDatabaseHas('users', [
            'name' => 'Admin User',
            'email' => 'admin@closer.com',
            'nivel_acesso' => 3, // Admin level
            'ativo' => true,
        ]);

        // Check if regular users exist
        $this->assertDatabaseHas('users', [
            'nivel_acesso' => 0, // Free level
            'ativo' => true,
        ]);

        // Check if premium users exist
        $this->assertDatabaseHas('users', [
            'nivel_acesso' => 2, // Premium level
            'ativo' => true,
        ]);
    }

    /** @test */
    public function database_seeder_runs_all_seeders_successfully()
    {
        Artisan::call('db:seed');

        $this->assertDatabaseCount('countries', 250);
        $this->assertDatabaseCount('states', 27);
        $this->assertGreaterThan(5000, City::count());
        $this->assertDatabaseCount('hobbies', 50);
        $this->assertDatabaseCount('languages', 20);
        $this->assertDatabaseCount('users', 10);
    }

    /** @test */
    public function seeders_create_relationships_correctly()
    {
        Artisan::call('db:seed');

        // Check that cities belong to correct states
        $spCity = City::where('name', 'São Paulo')->first();
        $spState = State::where('code', 'SP')->first();
        
        $this->assertNotNull($spCity);
        $this->assertNotNull($spState);
        $this->assertEquals($spState->id, $spCity->state_id);

        // Check that states belong to correct country
        $this->assertEquals($spState->country_id, Country::where('code', 'BR')->first()->id);

        // Check that users have profiles created
        $users = User::all();
        foreach ($users as $user) {
            $this->assertDatabaseHas('profiles', ['user_id' => $user->id]);
        }
    }

    /** @test */
    public function seeders_create_unique_records()
    {
        Artisan::call('db:seed');

        // Check that country codes are unique
        $countryCodes = Country::pluck('code');
        $this->assertEquals($countryCodes->count(), $countryCodes->unique()->count());

        // Check that state codes are unique within each country
        $states = State::all();
        foreach ($states->groupBy('country_id') as $countryId => $countryStates) {
            $stateCodes = $countryStates->pluck('code');
            $this->assertEquals($stateCodes->count(), $stateCodes->unique()->count());
        }

        // Check that city names are unique within each state
        foreach ($states as $state) {
            $cityNames = $state->cities()->pluck('name');
            $this->assertEquals($cityNames->count(), $cityNames->unique()->count());
        }

        // Check that hobby names are unique
        $hobbyNames = Hobby::pluck('name');
        $this->assertEquals($hobbyNames->count(), $hobbyNames->unique()->count());

        // Check that language codes are unique
        $languageCodes = Language::pluck('code');
        $this->assertEquals($languageCodes->count(), $languageCodes->unique()->count());

        // Check that user emails are unique
        $userEmails = User::pluck('email');
        $this->assertEquals($userEmails->count(), $userEmails->unique()->count());
    }

    /** @test */
    public function seeders_set_active_flags_correctly()
    {
        Artisan::call('db:seed');

        // Check that all seeded records are active by default
        $this->assertEquals(0, Country::where('active', false)->count());
        $this->assertEquals(0, State::where('active', false)->count());
        $this->assertEquals(0, City::where('active', false)->count());
        $this->assertEquals(0, Hobby::where('active', false)->count());
        $this->assertEquals(0, Language::where('active', false)->count());

        // Check that all seeded users are active
        $this->assertEquals(0, User::where('ativo', false)->count());
    }

    /** @test */
    public function seeder_is_idempotent()
    {
        // Run seeder first time
        Artisan::call('db:seed');
        $firstRunCounts = [
            'countries' => Country::count(),
            'states' => State::count(),
            'cities' => City::count(),
            'hobbies' => Hobby::count(),
            'languages' => Language::count(),
            'users' => User::count(),
        ];

        // Run seeder second time
        Artisan::call('db:seed');
        $secondRunCounts = [
            'countries' => Country::count(),
            'states' => State::count(),
            'cities' => City::count(),
            'hobbies' => Hobby::count(),
            'languages' => Language::count(),
            'users' => User::count(),
        ];

        // Counts should be the same (no duplicates)
        $this->assertEquals($firstRunCounts, $secondRunCounts);
    }
}
