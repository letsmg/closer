<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Shorts;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ConteudoSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {

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
}