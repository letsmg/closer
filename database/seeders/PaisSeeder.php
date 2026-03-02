<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pais;

class PaisSeeder extends Seeder
{
    public function run(): void
    {
        Pais::updateOrCreate(
            ['code' => 'BR'],
            ['sigla' => 'BRA']
        );

        $this->command->info('Países cadastrados com sucesso.');
    }
}