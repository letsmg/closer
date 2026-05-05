<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Profile;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Language;
use App\Models\Hobby;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeederUpdated extends Seeder
{
    public function run(): void
    {
        // Create admin users first
        $this->createAdminUsers();
        
        // Create regular users
        $this->createRegularUsers();
    }

    private function createAdminUsers(): void
    {
        $adminUsers = [
            [
                'name' => 'Main Administrator',
                'email' => 'admin@closer.com',
                'password' => Hash::make('Mudar@123'),
                'nivel_acesso' => 3, // Admin
                'ativo' => true,
            ],
            [
                'name' => 'System Moderator',
                'email' => 'moderator@closer.com', 
                'password' => Hash::make('Mudar@123'),
                'nivel_acesso' => 3, // Admin
                'ativo' => true,
            ],
            [
                'name' => 'Support Agent',
                'email' => 'support@closer.com',
                'password' => Hash::make('Mudar@123'),
                'nivel_acesso' => 5, // Support
                'ativo' => true,
            ],
            [
                'name' => 'Test User',
                'email' => 'teste@closer.com',
                'password' => Hash::make('Mudar@123'),
                'nivel_acesso' => 0, // Free user for testing
                'ativo' => true,
            ],
        ];

        foreach ($adminUsers as $adminUser) {
            User::create($adminUser);
        }
    }

    private function createRegularUsers(): void
    {
        // Get available data for seeding
        $cities = City::whereIn('name', ['São Paulo', 'Rio de Janeiro', 'Monte Belo'])->get();
        $languages = Language::all();
        $hobbies = Hobby::all();

        if ($cities->isEmpty()) {
            $this->command->error('No cities found. Run CitySeeder first.');
            return;
        }

        $levels = [0, 1, 2]; // Free, Plus, Premium
        $usersPerCity = 6;

        foreach ($cities as $city) {
            for ($i = 1; $i <= $usersPerCity; $i++) {
                $level = $levels[array_rand($levels)];
                $levelName = match($level) {
                    0 => 'Free',
                    1 => 'Plus', 
                    2 => 'Premium',
                };

                $cityNameClean = Str::slug($city->name, '');
                $levelNameClean = Str::slug($levelName, '');
                $email = "user" . ($i + ($city->id * 10)) . "@" . ($city->id + 1) . ".com";

                $user = User::updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => "User $levelName $i",
                        'password' => Hash::make('Mudar@123'),
                        'nivel_acesso' => $level,
                        'ativo' => true,
                    ]
                );

                // Create profile for the user
                $profile = Profile::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nickname' => $user->name,
                        'birth_date' => now()->subYears(rand(18, 35)),
                        'gender' => ['male', 'female', 'non_binary', 'other'][array_rand(['male', 'female', 'non_binary', 'other'])],
                        'gender_identity' => 'cisgender',
                        'sexual_orientation' => ['heterosexual', 'homosexual', 'bisexual', 'pansexual'][array_rand(['heterosexual', 'homosexual', 'bisexual', 'pansexual'])],
                        'purpose' => ['serious', 'casual', 'friendship', 'networking', 'all'][array_rand(['serious', 'casual', 'friendship', 'networking', 'all'])],
                        'profession' => ['Developer', 'Designer', 'Teacher', 'Doctor', 'Engineer'][array_rand(['Developer', 'Designer', 'Teacher', 'Doctor', 'Engineer'])],
                        'biography' => 'Passionate about life and new experiences.',
                        'smoker' => false,
                        'drinker' => rand(0, 1) === 1,
                        'marital_status' => ['single', 'married', 'divorced', 'widowed', 'open_relationship'][array_rand(['single', 'married', 'divorced', 'widowed', 'open_relationship'])],
                        'country_id' => $city->state->country_id,
                        'state_id' => $city->state_id,
                        'city_id' => $city->id,
                        'visibility' => 'public',
                        'latitude' => $city->latitude,
                        'longitude' => $city->longitude,
                    ]
                );

                // Add random languages to profile
                $selectedLanguages = $languages->random(rand(1, 3));
                foreach ($selectedLanguages as $language) {
                    $profile->languages()->attach($language->id, [
                        'level' => ['active', 'fluent', 'intermediate', 'basic'][array_rand(['active', 'fluent', 'intermediate', 'basic'])]
                    ]);
                }

                // Add random hobbies to profile
                $selectedHobbies = $hobbies->random(rand(2, 5));
                foreach ($selectedHobbies as $hobby) {
                    $profile->hobbies()->attach($hobby->id);
                }
            }
        }
    }
}
