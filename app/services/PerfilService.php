<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserPromotion;
use Illuminate\Support\Facades\DB;

class PerfilService
{
    public function update(User $user, array $dados)
    {
        return DB::transaction(function () use ($user, $dados) {

            $perfil = $user->perfil;

            // =========================
            // REGRA 1 - FOTO OBRIGATÓRIA
            // =========================
            if ($user->fotos()->count() === 0) {
                abort(403, 'Você precisa ter pelo menos uma foto.');
            }

            // =========================
            // REGRA 2 - TRAVA PROMOÇÃO
            // =========================
            if (isset($dados['visibilidade']) && $dados['visibilidade'] == true) {

                $temPromocao = UserPromotion::where('user_id', $user->id)
                    ->where('cumprindo_regras', true)
                    ->where('data_termino', '>', now())
                    ->exists();

                if ($temPromocao) {
                    abort(403, 'Sua promoção exige perfil visível.');
                }
            }

            // =========================
            // REGRA 3 - PONTOS VIAJANTE
            // =========================
            if (isset($dados['cidade_id']) && $perfil->cidade_id != $dados['cidade_id']) {

                if (!$perfil->updated_at || 
                    $perfil->updated_at->diffInHours(now()) >= 24) {
                    
                    $perfil->increment('pontos_viajante');
                }
            }

            // =========================
            // ATUALIZA HOBBIES
            // =========================
            if (isset($dados['hobbies'])) {
                $perfil->hobbies()->sync($dados['hobbies']);
            }

            // Remove hobbies do array para não tentar salvar na tabela perfis
            unset($dados['hobbies']);

            // =========================
            // ATUALIZA PERFIL
            // =========================
            $perfil->update($dados);

            return [
                'status' => 'success',
                'perfil' => $perfil->fresh()
            ];
        });
    }
}