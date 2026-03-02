<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\Pais;
use App\Models\Estado;
use App\Models\Cidade;

class CidadeSeeder extends Seeder
{
    public function run(): void
    {
        // 🔹 Username do GeoNames do .env, fallback 'nnluiz'
        $username = env('GEONAMES_USERNAME');

        // 🔹 Pega o país Brasil
        $pais = Pais::where('code', 'BR')->first();
        if (!$pais) {
            $this->command->error('Brasil não encontrado. Rode o PaisSeeder primeiro.');
            return;
        }

        // 🔹 Mapeamento sigla -> nome completo para o GeoNames
        $estadosGeoNames = [
            'SP' => 'São Paulo',
            'RJ' => 'Rio de Janeiro',
            'MG' => 'Minas Gerais',
        ];

        // 🔹 Cidades fixas
        $cidadesFixas = [
            ['nome' => 'São Paulo', 'uf' => 'SP'],
            ['nome' => 'Rio de Janeiro', 'uf' => 'RJ'],
            ['nome' => 'Monte Belo', 'uf' => 'MG'],
        ];

        foreach ($cidadesFixas as $cidadeInfo) {
            $estado = Estado::where('uf', $cidadeInfo['uf'])->first();

            if (!$estado) {
                $this->command->error("Estado {$cidadeInfo['uf']} não encontrado. Rode o EstadoSeeder primeiro.");
                continue;
            }

            $this->command->info("Buscando {$cidadeInfo['nome']} - {$cidadeInfo['uf']} no GeoNames...");

            try {
                $response = Http::timeout(10)->get("http://api.geonames.org/searchJSON", [
                    'name' => $cidadeInfo['nome'],
                    'country' => 'BR',
                    'featureClass' => 'P',
                    'maxRows' => 10,
                    'orderby' => 'population',
                    'username' => $username,
                ]);

                if (!$response->successful()) {
                    $this->command->warn("Erro ao buscar {$cidadeInfo['nome']}.");
                    continue;
                }

                $geonames = $response->json()['geonames'] ?? [];
                if (empty($geonames)) {
                    $this->command->warn("Nenhuma cidade encontrada para {$cidadeInfo['nome']}.");
                    continue;
                }
                // if ($cidadeInfo['nome'] === 'Monte Belo') {
                //     dd($geonames);
                // }              
                
                // 🔹 Filtra pelo estado correto usando o nome completo
                $cidadeApi = collect($geonames)->first(function ($c) use ($cidadeInfo) {
                    return
                        strtoupper($c['name'] ?? '') === strtoupper($cidadeInfo['nome']) &&
                        strtoupper($c['adminCodes1']['ISO3166_2'] ?? '') === strtoupper($cidadeInfo['uf']);
                });

                if (!$cidadeApi) {
                    $this->command->warn("Nenhum resultado do GeoNames bateu com o estado {$cidadeInfo['uf']} para {$cidadeInfo['nome']}.");
                    continue;
                }

                // 🔹 Salva no banco
                Cidade::updateOrCreate(
                    ['geoname_id' => $cidadeApi['geonameId']],
                    [
                        'nome' => $cidadeApi['name'],
                        'display_name' => $cidadeApi['name'] . " - {$cidadeInfo['uf']} (BR)",
                        'estado_id' => $estado->id,
                        'pais_code' => 'BR',
                        'latitude' => $cidadeApi['lat'],
                        'longitude' => $cidadeApi['lng'],
                    ]
                );

                $this->command->info("Cidade {$cidadeApi['name']} cadastrada com sucesso.");

            } catch (\Exception $e) {
                $this->command->error("Erro ao buscar {$cidadeInfo['nome']}: " . $e->getMessage());
            }
        }

        $this->command->info('Todas as cidades fixas foram processadas.');
    }
}