<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\UserMatch;
use App\Models\Mensagem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class AtualizarReputacaoInativos implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $this->penalizarInatividade();
        $this->penalizarGhosting();
    }

    /**
     * Penaliza usuários sem interação nas últimas 24h
     */
    private function penalizarInatividade(): void
    {
        User::where(function ($query) {
                $query->where('ultima_interacao_at', '<', now()->subDay())
                      ->orWhereNull('ultima_interacao_at');
            })
            ->chunkById(1000, function ($users) {

                foreach ($users as $user) {

                    if ($user->reputacao > 0) {
                        $user->update([
                            'reputacao' => max(0, $user->reputacao - 1)
                        ]);
                    }
                }
            });
    }

    /**
     * Penaliza ghosting (match sem mensagem após 48h)
     */
    private function penalizarGhosting(): void
    {
        $matchesAntigos = UserMatch::where('created_at', '<', now()->subHours(48))
            ->chunkById(1000, function ($matches) {

                foreach ($matches as $match) {

                    $usuarios = [$match->user_one_id, $match->user_two_id];

                    foreach ($usuarios as $userId) {

                        $enviouMensagem = Mensagem::where('user_match_id', $match->id)
                            ->where('sender_id', $userId)
                            ->exists();

                        if (!$enviouMensagem) {

                            $user = User::find($userId);

                            if ($user && $user->reputacao > 0) {
                                $user->update([
                                    'reputacao' => max(0, $user->reputacao - 2)
                                ]);
                            }
                        }
                    }
                }
            });
    }
}