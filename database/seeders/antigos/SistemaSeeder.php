<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Pais;
use App\Models\Estado;
use App\Models\Cidade;
use App\Models\Idioma;
use App\Models\Hobby;
use App\Models\Shorts;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class SistemaSeeder extends Seeder
{
    public function run(): void
    {
        // =============================
        // 1. LOCALIZAÇÃO
        // =============================

        $pais = Pais::create(['nome' => 'Brasil', 'sigla' => 'BR']);
        
        $sp = Estado::create(['nome' => 'São Paulo', 'uf' => 'SP', 'pais_id' => $pais->id]);
        $rj = Estado::create(['nome' => 'Rio de Janeiro', 'uf' => 'RJ', 'pais_id' => $pais->id]);

        $cidadeSP = Cidade::create(['nome' => 'São Paulo', 'estado_id' => $sp->id]);
        $cidadeRJ = Cidade::create(['nome' => 'Rio de Janeiro', 'estado_id' => $rj->id]);

        // =============================
        // 2. HOBBIES (CATÁLOGO)
        // =============================

        $hobbiesLista = [
            'Música','Esportes','Tecnologia','Culinária','Games',
            'Viagens','Cinema','Leitura','Academia','Praia'
        ];

        foreach ($hobbiesLista as $nome) {
            Hobby::firstOrCreate(['nome' => $nome]);
        }

        $todosHobbies = Hobby::all();

        // =============================
        // 3. IDIOMAS
        // =============================

        $idiomasDisponiveis = [
            ['codigo' => 'pt', 'nome' => 'Português'],
            ['codigo' => 'en', 'nome' => 'Inglês'],
            ['codigo' => 'es', 'nome' => 'Espanhol'],
        ];

        foreach ($idiomasDisponiveis as $idioma) {
            Idioma::firstOrCreate(['codigo' => $idioma['codigo']], $idioma);
        }

        $todosIdiomas = Idioma::all();

        // =============================
        // 4. USUÁRIOS
        // =============================

        $niveis = [0,1,2];

        foreach ($niveis as $nivel) {

            $tipoNome = match($nivel) {
                0 => 'Free',
                1 => 'Plus',
                2 => 'Premium',
            };
            
            for ($i = 1; $i <= 6; $i++) {

                $email = strtolower($tipoNome).$i."@closer.com";
                
                $user = User::create([
                    'name' => "Usuário $tipoNome $i",
                    'email' => $email,
                    'password' => Hash::make('12345678'),
                    'nivel_acesso' => $nivel,
                    'ativo' => true,
                ]);

                $isMasculino = ($i % 2 == 0);
                $sexo = $isMasculino ? 'masculino' : 'feminino';
                $busca = $isMasculino ? 'feminino' : 'masculino';

                // =============================
                // PERFIL
                // =============================

                $perfil = $user->perfil()->create([
                    'data_nascimento' => now()->subYears(rand(18, 40))->format('Y-m-d'),
                    'sexo' => $sexo,
                    'identidade_genero' => $isMasculino ? 'Homem Cis' : 'Mulher Cis',
                    'orientacao_sexual' => 'Hetero',                    
                    'pais_id' => $pais->id,
                    'estado_id' => $isMasculino ? $sp->id : $rj->id,
                    'cidade_id' => $isMasculino ? $cidadeSP->id : $cidadeRJ->id,
                    'visibilidade' => false,
                ]);

                // =============================
                // PERFIL PREFERENCIAS (1:1)
                // =============================

                $perfil->preferencia()->create([
                    'sexo' => $busca,
                    'identidade_genero' => null,
                    'orientacao_sexual' => null,
                    'objetivo' => 'Relacionamento sério',
                    'fumante' => false,
                    'bebida' => true,
                    'estado_civil' => 'Solteiro',
                    'pais_id' => $pais->id,
                    'estado_id' => null,
                    'cidade_id' => null,
                    'idade_minima' => 18,
                    'idade_maxima' => 35,
                    'visibilidade' => false,
                ]);

                // =============================
                // IDIOMAS QUE FALA
                // =============================

                $idiomasFalados = $todosIdiomas->shuffle()->take(2);

                foreach ($idiomasFalados as $idioma) {
                    $perfil->idiomas()->syncWithoutDetaching([
                        $idioma->id => ['nivel' => 'intermediario']
                    ]);
                }

                // =============================
                // IDIOMAS QUE BUSCA
                // =============================

                $idiomasBusca = $todosIdiomas->shuffle()->take(1);

                foreach ($idiomasBusca as $idioma) {
                    if (!$perfil->idiomasPreferidos()
                            ->where('idioma_id', $idioma->id)
                            ->exists()) {
                        $perfil->idiomasPreferidos()->attach($idioma->id);
                    }
                }

                // =============================
                // HOBBIES (N:N)
                // =============================

                $hobbiesSelecionados = $todosHobbies->shuffle()->take(3);

                foreach ($hobbiesSelecionados as $hobby) {
                    $perfil->hobbies()->syncWithoutDetaching([$hobby->id]);
                }

                // =============================
                // SHORTS
                // =============================

                for ($p = 1; $p <= 2; $p++) {

                    Shorts::create([
                        'user_id'  => $user->id,
                        'conteudo' => $p == 1 ? "Qual sua série favorita?" : "Café ou Chá?",
                        'tipo'     => 'q',
                        'posicao'  => $p
                    ]);

                    Shorts::create([
                        'user_id'  => $user->id,
                        'conteudo' => $p == 1 ? "Adoro Breaking Bad!" : "Prefiro um café forte!",
                        'tipo'     => 'r',
                        'posicao'  => $p
                    ]);
                }

                // =============================
                // FOTOS
                // =============================

                $diretorioUser = "fotos/user_{$user->id}";
                Storage::disk('public')->makeDirectory($diretorioUser);

                for ($f = 1; $f <= 3; $f++) {

                    $nomeFoto = "seed_foto_{$f}.jpg";
                    $pathCompleto = "{$diretorioUser}/{$nomeFoto}";

                    try {
                        $imagemUrl = "https://i.pravatar.cc/600?u=".$user->id.$f;
                        $conteudoImagem = Http::get($imagemUrl)->body();
                        Storage::disk('public')->put($pathCompleto, $conteudoImagem);

                        $user->fotos()->create([
                            'path' => $pathCompleto,
                            'is_principal' => ($f === 1),
                            'ordem' => $f
                        ]);

                    } catch (\Exception $e) {
                        $this->command->error("Erro ao baixar foto para usuário {$user->id}");
                    }
                }
            }
        }

        $this->command->info('Sucesso! 18 usuários criados corretamente.');
    }
}