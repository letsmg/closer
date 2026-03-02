<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserMatch;
use Carbon\Carbon;

class CalcularGhosting extends Command
{
    // O nome que você usará no terminal para testar
    protected $signature = 'closer:ghosting';
    protected $description = 'Penaliza usuários que não respondem mensagens após um Match';

    public function handle()
    {
        // 1. Pegar matches criados há mais de 48 horas
        $matchesAntigos = UserMatch::where('created_at', '<=', Carbon::now()->subHours(48))
            ->with('mensagens')
            ->get();

        foreach ($matchesAntigos as $match) {
            // Se o match não tem nenhuma mensagem...
            if ($match->mensagens->count() === 0) {
                // Ambos ganham ponto de ghosting? 
                // Geralmente penalizamos quem foi o "user_two" (quem recebeu o like e deu o match)
                // pois ele foi o último a agir e não iniciou a conversa.
                
                $alvo = \App\Models\User::find($match->user_two_id);
                if ($alvo && $alvo->perfil) {
                    $alvo->perfil->increment('pontos_ghosting');
                    $this->info("Ponto de ghosting atribuído a: {$alvo->name}");
                }
                
                // Opcional: Deletar o match morto para limpar o banco
                // $match->delete();
            }
        }

        $this->info('Processamento de Ghosting finalizado.');
    }
}