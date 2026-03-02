<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Idioma;

class IdiomaSeeder extends Seeder
{
    public function run(): void
    {
        $idiomas = [
            ['codigo' => 'pt', 'nome' => 'Português'],
            ['codigo' => 'en', 'nome' => 'Inglês'],
            ['codigo' => 'es', 'nome' => 'Espanhol'],
        ];

        foreach ($idiomas as $idioma) {
            Idioma::updateOrCreate(
                ['codigo' => $idioma['codigo']],
                $idioma
            );
        }
    }
}