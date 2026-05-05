<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        Country::updateOrCreate(
            ['code' => 'BR'],
            ['abbreviation' => 'BRA']
        );

        $this->command->info('Countries created successfully.');
    }
}
