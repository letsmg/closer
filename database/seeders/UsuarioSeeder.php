<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Cidade;
use App\Models\Idioma;
use App\Models\Hobby;
use App\Models\Perfil;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        // 🔹 Busca as cidades disponíveis (Tenta as específicas, se não achar, pega as que tiver)
        $cidades = Cidade::whereIn('nome', ['São Paulo', 'Rio de Janeiro', 'Monte Belo'])->get();
        
        if ($cidades->isEmpty()) {
            $cidades = Cidade::take(3)->get();
        }

        $idiomas = Idioma::all();
        $hobbies = Hobby::all();

        if ($cidades->isEmpty()) {
            $this->command->error('Nenhuma cidade encontrada no banco. Rode o CidadeSeeder primeiro.');
            return;
        }

        $niveis = [0, 1, 2]; // Free, Plus, Premium
        $usuariosPorCidade = 6;

        foreach ($cidades as $cidade) {
            for ($i = 1; $i <= $usuariosPorCidade; $i++) {

                $nivel = $niveis[array_rand($niveis)];
                $tipoNome = match($nivel) {
                    0 => 'Free',
                    1 => 'Plus',
                    2 => 'Premium',
                };

                $nomeCidadeLimpo = Str::slug($cidade->nome, ''); 
                $tipoNomeLimpo = Str::slug($tipoNome, '');
                $email = strtolower($tipoNomeLimpo) . $i . "_" . rand(100, 999) . "@" . $nomeCidadeLimpo . ".com";

                $user = User::updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => "Usuário $tipoNome $i",
                        'password' => Hash::make('12345678'),
                        'nivel_acesso' => $nivel,
                        'ativo' => true,
                    ]
                );

                $isMasculino = rand(0, 1) === 0;
                $sexo = $isMasculino ? 'masculino' : 'feminino';
                $busca = $isMasculino ? 'feminino' : 'masculino';

                $perfil = Perfil::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'apelido' => "Apelido $tipoNome $i",
                        'data_nascimento' => now()->subYears(rand(18, 40))->format('Y-m-d'),
                        'sexo' => $sexo,
                        'identidade_genero' => $isMasculino ? 'Homem Cis' : 'Mulher Cis',
                        'orientacao_sexual' => 'Hetero',
                        'bebida' => (bool)rand(0, 1),
                        'fumante' => (bool)rand(0, 1),
                        'estado_civil' => 'Solteiro',
                        'profissao' => "Profissão $tipoNome $i",
                        'biografia' => "Biografia do usuário $tipoNome $i. Gosta de viajar e conhecer pessoas novas.",
                        'cidade_id' => $cidade->id,
                        'estado_id' => $cidade->estado_id,
                        'pais_id' => $cidade->estado->pais_id ?? null,
                        'latitude' => $cidade->latitude,
                        'longitude' => $cidade->longitude,
                        'visibilidade' => collect(['publico', 'invisivel', 'somente_match'])->random(),
                    ]
                );

                // PREFERÊNCIAS (Filtros de Match)
                $perfil->preferencia()->updateOrCreate(
                    ['perfil_id' => $perfil->id],
                    [
                        'sexo' => $busca,
                        'objetivo' => collect(['serio','casual','amizade','networking','todos'])->random(),
                        'orientacao_sexual' => collect(['Hetero','Homosexual','Bissexual','Assexual','Pansexual'])->random(),
                        'idade_minima' => 18,
                        'idade_maxima' => 45,
                        'raio_busca_km' => 50,
                    ]
                );

                // IDIOMAS (O que eu falo)
                if ($idiomas->isNotEmpty()) {
                    $perfil->idiomas()->sync(
                        $idiomas->random(min(2, $idiomas->count()))->mapWithKeys(fn($idioma) => [
                            $idioma->id => ['nivel' => 'intermediario']
                        ])->toArray()
                    );

                    // IDIOMAS BUSCADOS (O que eu quero que o match fale)
                    $perfil->idiomasBuscados()->sync(
                        $idiomas->random(min(1, $idiomas->count()))->pluck('id')->toArray()
                    );
                }

                // HOBBIES (O que eu faço)
                if ($hobbies->isNotEmpty()) {
                    $perfil->hobbies()->sync(
                        $hobbies->random(min(3, $hobbies->count()))->pluck('id')->toArray()
                    );

                    // HOBBIES BUSCADOS (O que eu quero que o match goste)
                    $perfil->hobbiesBuscados()->sync(
                        $hobbies->random(min(2, $hobbies->count()))->pluck('id')->toArray()
                    );
                }
            }
        }

        $this->command->info('Todos os usuários foram criados com sucesso.');
    }
}