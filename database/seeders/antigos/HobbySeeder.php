<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hobby;

class HobbySeeder extends Seeder
{
    public function run(): void
    {
        $hobbies = [

            //Geek
            ['nome'=>'Animes','categoria'=>'Geek'],
            ['nome'=>'Séries','categoria'=>'Geek'],
            ['nome'=>'Filmes','categoria'=>'Geek'],
            ['nome'=>'Quadrinhos','categoria'=>'Geek'],
            ['nome'=>'Cosplay','categoria'=>'Geek'],
            ['nome'=>'Tecnologia','categoria'=>'Geek'],

            //Ciência
            ['nome'=>'Astronomia','categoria'=>'Ciência'],
            ['nome'=>'Biologia','categoria'=>'Ciência'],
            ['nome'=>'Química','categoria'=>'Ciência'],
            ['nome'=>'Física','categoria'=>'Ciência'],
            ['nome'=>'Matemática','categoria'=>'Ciência'],
            
            // Esportes
            ['nome'=>'Futebol','categoria'=>'Esporte'],
            ['nome'=>'Academia','categoria'=>'Esporte'],
            ['nome'=>'Corrida','categoria'=>'Esporte'],
            ['nome'=>'Ciclismo','categoria'=>'Esporte'],
            ['nome'=>'Natação','categoria'=>'Esporte'],
            ['nome'=>'Yoga','categoria'=>'Esporte'],
            ['nome'=>'Crossfit','categoria'=>'Esporte'],
            ['nome'=>'Surf','categoria'=>'Esporte'],

            // Cultura
            ['nome'=>'Cinema','categoria'=>'Cultura'],
            ['nome'=>'Teatro','categoria'=>'Cultura'],
            ['nome'=>'Museus','categoria'=>'Cultura'],
            ['nome'=>'Leitura','categoria'=>'Cultura'],
            ['nome'=>'Escrita','categoria'=>'Cultura'],

            // Música
            ['nome'=>'Tocar Violão','categoria'=>'Música'],
            ['nome'=>'Cantar','categoria'=>'Música'],
            ['nome'=>'Shows','categoria'=>'Música'],
            ['nome'=>'Produção Musical','categoria'=>'Música'],

            // Tecnologia
            ['nome'=>'Programação','categoria'=>'Tecnologia'],
            ['nome'=>'Games','categoria'=>'Tecnologia'],
            ['nome'=>'E-sports','categoria'=>'Tecnologia'],
            ['nome'=>'Criptomoedas','categoria'=>'Tecnologia'],
            ['nome'=>'IA','categoria'=>'Tecnologia'],

            // Social
            ['nome'=>'Viagens','categoria'=>'Social'],
            ['nome'=>'Culinária','categoria'=>'Social'],
            ['nome'=>'Barzinho','categoria'=>'Social'],
            ['nome'=>'Café','categoria'=>'Social'],
            ['nome'=>'Voluntariado','categoria'=>'Social'],

            // Outdoor
            ['nome'=>'Trilhas','categoria'=>'Outdoor'],
            ['nome'=>'Camping','categoria'=>'Outdoor'],
            ['nome'=>'Escalada','categoria'=>'Outdoor'],
            ['nome'=>'Fotografia','categoria'=>'Outdoor'],
            ['nome'=>'Observação de Estrelas','categoria'=>'Outdoor'],
                 

            // Lifestyle
            ['nome'=>'Moda','categoria'=>'Lifestyle'],
            ['nome'=>'Maquiagem','categoria'=>'Lifestyle'],
            ['nome'=>'Pets','categoria'=>'Lifestyle'],
            ['nome'=>'Meditação','categoria'=>'Lifestyle'],
            ['nome'=>'Astrologia','categoria'=>'Lifestyle'],
            ['nome'=>'Investimentos','categoria'=>'Lifestyle'],
            ['nome'=>'Empreendedorismo','categoria'=>'Lifestyle'],

            // Extras
            ['nome'=>'Podcasts','categoria'=>'Extra'],
            ['nome'=>'Stand-up','categoria'=>'Extra'],
            ['nome'=>'Dança','categoria'=>'Extra'],
            ['nome'=>'Artesanato','categoria'=>'Extra'],
            ['nome'=>'Desenho','categoria'=>'Extra'],
            ['nome'=>'Pintura','categoria'=>'Extra'],
            ['nome'=>'TikTok','categoria'=>'Extra'],
            ['nome'=>'YouTube','categoria'=>'Extra'],
            ['nome'=>'Board Games','categoria'=>'Extra'],
            ['nome'=>'Carros','categoria'=>'Extra'],
            ['nome'=>'Motociclismo','categoria'=>'Extra'],
        ];

        foreach ($hobbies as $hobby) {
            Hobby::firstOrCreate(['nome'=>$hobby['nome']],$hobby);
        }
    }
}