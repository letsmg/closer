<?php

namespace Database\Seeders;

use App\Enums\UserLevel;
use App\Models\User;
use App\Models\Profile;
use App\Models\ProfilePreference;
use App\Models\City;
use App\Models\Hobby;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Cria perfis e preferências para todos os usuários não-staff
     */
    public function run(): void
    {
        // Pega todos os usuarios que nao sao staff e que nao tem perfil ainda
        $users = User::where('nivel_acesso', '<', UserLevel::ADMIN->value)
            ->whereDoesntHave('perfil')
            ->get();

        if ($users->isEmpty()) {
            $this->command->info('Todos os usuários já possuem perfil.');
            return;
        }

        $cities = City::all();
        $hobbies = Hobby::all();
        $genders = ['male', 'female', 'non_binary'];
        $orientations = ['heterosexual', 'homosexual', 'bisexual', 'pansexual', 'todos'];
        $purposes = ['friendship', 'casual', 'all'];
        $gendersIdentity = ['male', 'female', 'non_binary', 'other'];
        $smokerOptions = [true, false, null];
        $drinkerOptions = [true, false, null];
        $maritalStatus = ['single', 'married', 'divorced', 'widowed', 'open_relationship'];

        foreach ($users as $index => $user) {
            $city = $cities->random();
            $gender = $genders[array_rand($genders)];
            $birthDate = Carbon::now()->subYears(rand(18, 45))->subDays(rand(0, 365));

            // Remove caracteres especiais do nome para criar nickname
            $nameParts = explode(' ', $user->name);
            $firstName = $nameParts[0];
            $nickname = $firstName . rand(10, 99);

            // Profile
            $profile = Profile::create([
                'user_id' => $user->id,
                'uuid' => (string) Str::ulid(),
                'nickname' => $nickname,
                'birth_date' => $birthDate,
                'gender' => $gender,
                'gender_identity' => $gendersIdentity[array_rand($gendersIdentity)],
                'sexual_orientation' => $orientations[array_rand($orientations)],
                'purpose' => $purposes[array_rand($purposes)],
                'profession' => $this->getRandomProfession(),
                'biography' => $this->getRandomBiography(),
                'smoker' => $smokerOptions[array_rand($smokerOptions)],
                'drinker' => $drinkerOptions[array_rand($drinkerOptions)],
                'marital_status' => $maritalStatus[array_rand($maritalStatus)],
                'country_id' => 1, // Brasil
                'state_id' => $city->state_id,
                'city_id' => $city->id,
                'visibility' => 'public',
                'reputacao' => rand(1, 5),
                'ultima_interacao_at' => now()->subHours(rand(0, 72)),
            ]);

            // ProfilePreference
            ProfilePreference::create([
                'profile_id' => $profile->id,
                'gender' => $genders[array_rand($genders)],
                'sexual_orientation' => $orientations[array_rand($orientations)],
                'purpose' => $purposes[array_rand($purposes)],
                'smoker' => $smokerOptions[array_rand($smokerOptions)],
                'drinker' => $drinkerOptions[array_rand($drinkerOptions)],
                'marital_status' => $maritalStatus[array_rand($maritalStatus)],
                'search_radius_km' => rand(10, 100),
                'min_age' => rand(18, 25),
                'max_age' => rand(30, 50),
                'visibility' => 'public',
                'hide_location' => false,
                'invisible_mode' => false,
            ]);

            // Hobbies (2-5 aleatórios)
            $profileHobbies = $hobbies->random(rand(2, 5));
            $profile->hobbies()->attach($profileHobbies->pluck('id')->toArray());

            $this->command->info("Perfil criado para: {$user->email} ({$nickname})");
        }

        $this->command->info(count($users) . ' perfis criados com sucesso!');
    }

    private function getRandomProfession(): string
    {
        $professions = [
            'Desenvolvedor de Software', 'Designer Gráfico', 'Médico', 'Advogado',
            'Professor', 'Engenheiro', 'Arquiteto', 'Jornalista', 'Fotógrafo',
            'Psicólogo', 'Enfermeiro', 'Publicitário', 'Veterinário', 'Chef de Cozinha',
            'Músico', 'Artista Plástico', 'Personal Trainer', 'Consultor', 'Empresário',
            'Estudante', 'Analista de Marketing', 'Cientista de Dados', 'UX Designer',
        ];
        return $professions[array_rand($professions)];
    }

    private function getRandomBiography(): string
    {
        $bios = [
            'Apaixonado por viagens e novas amizades!',
            'Amante de café, livros e boas conversas.',
            'Vivendo um dia de cada vez com um sorriso no rosto.',
            'Procuro conexões genuínas e momentos inesquecíveis.',
            'Aventureiro nas horas vagas, caseiro nas outras.',
            'Música é minha paixão, e você?',
            'Fotógrafo amador, viajante profissional (nas férias).',
            'Entre um treino e outro, sempre tem espaço para um bom papo.',
            'A vida é curta demais para não aproveitar cada momento.',
            'Buscando alguém para compartilhar risadas e histórias.',
            'Apaixonado por natureza e animais.',
            'Cinema, séries e pizza nos dias de folga.',
        ];
        return $bios[array_rand($bios)];
    }
}
