<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Perfil;
use App\Models\Idioma;
use Illuminate\Support\Facades\DB;

class PerfilIdiomaSeeder extends Seeder
{
    public function run(): void
    {
        $perfis = Perfil::all();
        $idiomas = Idioma::all();

        foreach ($perfis as $perfil) {

            // Idiomas que fala (1 ou 2 aleatórios)
            $idiomasFalados = $idiomas->random(rand(1, 2));

            foreach ($idiomasFalados as $idioma) {
                DB::table('perfil_idiomas')->insert([
                    'perfil_id' => $perfil->id,
                    'idioma_id' => $idioma->id,
                    'nivel' => collect(['nativo','fluente','intermediario','basico'])->random(),
                ]);
            }

            // Idiomas que busca (1 ou 2 diferentes)
            $idiomasBusca = $idiomas->random(rand(1, 2));

            foreach ($idiomasBusca as $idioma) {
                DB::table('perfil_idiomas_preferencias')->insert([
                    'perfil_id' => $perfil->id,
                    'idioma_id' => $idioma->id,
                ]);
            }
        }
    }
}