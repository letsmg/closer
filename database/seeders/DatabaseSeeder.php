<?php

// namespace Database\Seeders;

// use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
// use Illuminate\Database\Seeder;

// class DatabaseSeeder extends Seeder
// {
//     use WithoutModelEvents;

//     /**
//      * Seed the application's database.
//      */
//     public function run(): void
//     {
//         // É aqui que você "chama" os outros seeders
//         $this->call([
//             LocalidadesBrasilSeeder::class,
//             SistemaSeeder::class,                      
//             HobbySeeder::class,

//             // Adicione outros aqui, como UserSeeder::class se tiver
//         ]);
//     }
// }


namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Database\Seeders\HobbySeeder;
use Database\Seeders\IdiomaSeeder;
use Database\Seeders\UsuarioSeeder;
use Database\Seeders\ConteudoSeeder;
use Database\Seeders\CidadeSeeder;
use Database\Seeders\EstadoSeeder;
use Database\Seeders\PaisSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PaisSeeder::class,
            EstadoSeeder::class,
            CidadeSeeder::class,
            IdiomaSeeder::class,
            HobbySeeder::class,
            UsuarioSeeder::class,
            ConteudoSeeder::class,
        ]);
    }
}