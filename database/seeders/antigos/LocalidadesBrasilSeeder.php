<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pais;
use App\Models\Estado;
use App\Models\Cidade;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class LocalidadesBrasilSeeder extends Seeder
{
    public function run()
    {
        // Desativa logs de query para economizar memória durante o loop grande
        DB::connection()->disableQueryLog();

        // 1. Criar ou buscar o Brasil
        $pais = Pais::firstOrCreate(['sigla' => 'BR'], ['nome' => 'Brasil']);

        $this->command->info('Conectando ao IBGE para buscar estados...');

        // 2. Buscar Estados
        $responseEstados = Http::get("https://servicodados.ibge.gov.br/api/v1/localidades/estados?orderBy=nome");
        
        if ($responseEstados->failed()) {
            $this->command->error('Falha ao conectar com a API do IBGE.');
            return;
        }

        $estados = $responseEstados->object();

        foreach ($estados as $est) {
            $this->command->info("Processando: {$est->nome}");

            // Criar Estado (usamos uf ou sigla conforme sua migration)
            $novoEstado = Estado::firstOrCreate(
                ['uf' => $est->sigla], 
                [
                    'pais_id' => $pais->id,
                    'nome' => $est->nome,
                ]
            );

            // 3. Buscar Cidades deste Estado
            // $responseCidades = Http::get("https://servicodados.ibge.gov.br/api/v1/localidades/estados/{$est->id}/municipios");
            
            // if ($responseCidades->successful()) {
            //     $cidades = $responseCidades->object();
            //     $dataCidades = [];

            //     foreach ($cidades as $cid) {
            //         $dataCidades[] = [
            //             'estado_id' => $novoEstado->id,
            //             'nome' => $cid->nome,
            //             'created_at' => now(),
            //             'updated_at' => now(),
            //         ];
            //     }

            //     // Inserção em massa (muito mais rápido que Cidade::create)
            //     // Inserimos em pedaços de 200 para não sobrecarregar o SQL
            //     foreach (array_chunk($dataCidades, 200) as $chunk) {
            //         Cidade::insert($chunk);
            //     }
            // }
        }

        $this->command->info('Sucesso! Brasil, Estados e Cidades importados.');
    }
}