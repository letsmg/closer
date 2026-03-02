<?php 

namespace App\Services;

use App\Models\User;
use App\Models\Bloqueio;
use Illuminate\Support\Facades\DB;

class ReputacaoService
{
    public function registrarInteracao(User $user): void
    {
        $user->increment('reputacao', 1);
        $user->ultima_interacao_at = now();
        $user->save();
    }

    public function registrarMensagem(User $user): void
    {
        $user->increment('reputacao', 1);
        $user->ultima_conversa_at = now();
        $user->save();
    }

    public function verificarBloqueios(int $perfilId): void
    {
        $bloqueiosRecentes = Bloqueio::where('perfil_bloqueado_id', $perfilId)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        if ($bloqueiosRecentes >= 5) {
            $user = User::find($perfilId);
            if ($user) {
                $user->decrement('reputacao', 10);
            }
        }
    }
}