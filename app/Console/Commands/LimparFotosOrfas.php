<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\FotoPerfil;

class LimparFotosOrfas extends Command
{
    protected $signature = 'closer:limpar-fotos';
    protected $description = 'Remove arquivos de fotos no storage que não possuem registro no banco';

    public function handle()
    {
        $this->info('Iniciando limpeza de arquivos órfãos...');

        // 1. Pegar todos os caminhos de fotos registrados no banco
        $fotosNoBanco = FotoPerfil::pluck('path')->toArray();

        // 2. Listar todos os arquivos dentro da pasta 'public/fotos'
        // Percorremos as subpastas de usuários
        $diretorios = Storage::disk('public')->directories('fotos');

        foreach ($diretorios as $diretorio) {
            $arquivosNoDisco = Storage::disk('public')->files($diretorio);

            foreach ($arquivosNoDisco as $arquivo) {
                // 3. Se o arquivo existe no disco mas NÃO está no banco, deletamos
                if (!in_array($arquivo, $fotosNoBanco)) {
                    Storage::disk('public')->delete($arquivo);
                    $this->warn("Arquivo removido: {$arquivo}");
                }
            }
        }

        $this->info('Limpeza concluída com sucesso!');
    }
}