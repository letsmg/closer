<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\HobbySeeder;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\CitySeeder;
use Database\Seeders\StateSeeder;
use Database\Seeders\CountrySeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CountrySeeder::class,
            StateSeeder::class,
            CitySeeder::class,
            LanguageSeeder::class,
            HobbySeeder::class,
            UserSeeder::class,  // Staff users (admin, operator, etc.)
            ReportSeeder::class,
            ProfileSeeder::class, // Profiles + Preferences + Hobbies for regular users
        ]);
    }
}
