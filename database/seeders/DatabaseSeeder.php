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
        
        // 🔒 Define token_version único baseado em timestamp para cada seed
        // Isso invalida automaticamente todos os JWTs emitidos ANTES deste seed
        // O HybridAuth verifica se o token_version do JWT corresponde ao do banco
        $seedVersion = now()->timestamp;
        \App\Models\User::query()->update(['token_version' => $seedVersion]);
        
        $this->command->info("Token version definido para: {$seedVersion}");
    }
}
