<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\Pais;
use App\Models\Estado;
use App\Models\Cidade;

class LocalizacaoSeeder extends Seeder
{
    public function run(): void
    {
        //o nome de usuario foi colocado no .env para facilitar a troca caso seja necessário
        $username = env('GEONAMES_USERNAME', 'defaultUsername');

        // ✅ Garante que o Brasil exista
        $pais = Pais::firstOrCreate(
            ['code' => 'BR'],
            ['sigla' => 'BRA']
        );

        // ✅ Busca estados do Brasil
        $estados = Estado::where('pais_id', $pais->id)->get();

        if ($estados->isEmpty()) {
            $this->command->error('Nenhum estado encontrado para BR.');
            return;
        }

        foreach ($estados as $estado) {

            $this->command->info("Buscando cidades de {$estado->nome}");

            try {

                $response = Http::timeout(10)->get(
                    "http://api.geonames.org/searchJSON",
                    [
                        'adminCode1' => $estado->uf,
                        'country' => 'BR',
                        'featureClass' => 'P',
                        'maxRows' => 50,
                        'orderby' => 'population',
                        'username' => $username
                    ]
                );

                if (!$response->successful()) {
                    $this->command->warn("Erro HTTP no estado {$estado->uf}");
                    continue;
                }

                $cidades = $response->json()['geonames'] ?? [];

                if (empty($cidades)) {
                    $this->command->warn("Nenhuma cidade encontrada para {$estado->uf}");
                    continue;
                }

                foreach ($cidades as $cidadeApi) {

                    if (!isset($cidadeApi['lat'], $cidadeApi['lng'])) {
                        continue;
                    }

                    Cidade::updateOrCreate(
                        ['geoname_id' => $cidadeApi['geonameId']],
                        [
                            'nome' => $cidadeApi['name'],
                            'display_name' => $cidadeApi['name'] . " - {$estado->uf} (BR)",
                            'estado_id' => $estado->id,
                            'pais_code' => 'BR',
                            'latitude' => $cidadeApi['lat'],
                            'longitude' => $cidadeApi['lng'],
                        ]
                    );
                }

            } catch (\Exception $e) {
                $this->command->error(
                    "Erro no estado {$estado->uf}: " . $e->getMessage()
                );
            }
        }

        $this->command->info('LocalizacaoSeeder finalizado.');
    }
}