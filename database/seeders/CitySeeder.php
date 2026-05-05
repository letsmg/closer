<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\Country;
use App\Models\State;
use App\Models\City;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        // GeoNames username from .env, fallback 'nnluiz'
        $username = env('GEONAMES_USERNAME');

        // Get Brazil country
        $country = Country::where('code', 'BR')->first();
        if (!$country) {
            $this->command->error('Brazil not found. Run CountrySeeder first.');
            return;
        }

        // Mapping abbreviation -> full name for GeoNames
        $statesGeoNames = [
            'SP' => 'São Paulo',
            'RJ' => 'Rio de Janeiro',
            'MG' => 'Minas Gerais',
        ];

        // Fixed cities
        $citiesFixed = [
            ['name' => 'São Paulo', 'uf' => 'SP'],
            ['name' => 'Rio de Janeiro', 'uf' => 'RJ'],
            ['name' => 'Monte Belo', 'uf' => 'MG'],
        ];

        foreach ($citiesFixed as $cityInfo) {
            $state = State::where('uf', $cityInfo['uf'])->first();
            
            City::updateOrCreate(
                ['name' => $cityInfo['name']],
                [
                    'display_name' => $cityInfo['name'],
                    'state_id' => $state->id,
                    'country_code' => 'BR',
                    'geoname_id' => $this->getGeonameId($cityInfo['name'], $username),
                    'latitude' => $this->getCityLatitude($cityInfo['name']),
                    'longitude' => $this->getCityLongitude($cityInfo['name']),
                ]
            );
        }

        $this->command->info('Cities created successfully.');
    }

    private function getGeonameId($cityName, $username)
    {
        // Try to get GeoNames ID for the city
        try {
            $response = Http::get("http://api.geonames.org/search", [
                'q' => $cityName,
                'country' => 'BR',
                'maxRows' => 1,
                'username' => $username
            ]);

            if ($response->successful() && !empty($response->json())) {
                return $response->json()[0]['geonameId'] ?? null;
            }
        } catch (\Exception $e) {
            $this->command->warn("Failed to get GeoNames ID for {$cityName}: {$e->getMessage()}");
        }

        return null;
    }

    private function getCityLatitude($cityName)
    {
        $coordinates = [
            'São Paulo' => -23.5505,
            'Rio de Janeiro' => -22.9068,
            'Monte Belo' => -19.9167,
        ];

        return $coordinates[$cityName] ?? null;
    }

    private function getCityLongitude($cityName)
    {
        $coordinates = [
            'São Paulo' => -46.6333,
            'Rio de Janeiro' => -43.1729,
            'Monte Belo' => -43.9345,
        ];

        return $coordinates[$cityName] ?? null;
    }
}
