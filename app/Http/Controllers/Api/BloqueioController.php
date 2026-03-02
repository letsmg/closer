<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Bloqueio;
use App\Models\Denuncia;
use App\Models\UserMatch;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\ReputacaoService;


class BloqueioController extends Controller
{
    /**
     * Registra um bloqueio e, opcionalmente, uma denúncia.
     */
    public function store(Request $request)
    {
        // 1. Validação rigorosa
        $request->validate([
            'blocked_user_id' => 'required|exists:users,id',
            'denuncia'        => 'boolean', 
            'motivo'          => 'required_if:denuncia,true|in:importunacao,desrespeito,perfil_falso,outro',
            'descricao'       => 'nullable|string|max:500'
        ]);

        $user = $request->user();
        $targetId = $request->blocked_user_id;

        // Impede auto-bloqueio
        if ($user->id == $targetId) {
            return response()->json(['error' => 'Operação inválida.'], 400);
        }

        return DB::transaction(function () use ($user, $targetId, $request) {
            
            // 2. Registrar o Bloqueio
            // Usamos firstOrCreate para evitar erro de duplicidade se o usuário clicar duas vezes
            Bloqueio::firstOrCreate([
                'user_id' => $user->id,
                'blocked_user_id' => $targetId
            ]);

            // 3. Processar Denúncia e Pontuação (Karma Negativo)
            if ($request->denuncia) {
                Denuncia::create([
                    'denunciante_id' => $user->id,
                    'denunciado_id'  => $targetId,
                    'motivo'         => $request->motivo,
                    'descricao'      => $request->descricao
                ]);

                // Incrementa pontos_bloqueio no perfil do alvo
                $targetUser = User::find($targetId);
                if ($targetUser && $targetUser->perfil) {
                    $targetUser->perfil->increment('pontos_bloqueio');
                    
                    // Auto-ban: Se atingir 50 pontos de bloqueio, a conta é suspensa
                    if ($targetUser->perfil->pontos_bloqueio >= 50) {
                        $targetUser->update(['ativo' => false]);
                    }
                }
            }

            // 4. LIMPEZA TOTAL DE INTERAÇÕES (Obrigatório para privacidade)
            
            // Remove qualquer Like/Dislike entre os dois
            Like::where(function($q) use ($user, $targetId) {
                $q->where('user_id', $user->id)->where('liked_user_id', $targetId);
            })->orWhere(function($q) use ($user, $targetId) {
                $q->where('user_id', $targetId)->where('liked_user_id', $user->id);
            })->delete();

            // Remove o Match e, consequentemente, as mensagens (via cascade no banco)
            UserMatch::where(function($q) use ($user, $targetId) {
                $q->where('user_one_id', $user->id)->where('user_two_id', $targetId);
            })->orWhere(function($q) use ($user, $targetId) {
                $q->where('user_one_id', $targetId)->where('user_two_id', $user->id);
            })->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Usuário bloqueado e interações removidas.'
            ]);
        });
    }

    /**
     * Lista de usuários bloqueados pelo usuário autenticado.
     */
    public function index(Request $request)
    {
        $bloqueados = Bloqueio::where('user_id', $request->user()->id)
            ->with(['bloqueado' => function($q) {
                $q->select('id', 'name')->with('fotos');
            }])
            ->get();

        return response()->json($bloqueados);
    }

    /**
     * Desbloquear um usuário.
     */
    public function destroy(Request $request, $blockedUserId)
    {
        Bloqueio::where('user_id', $request->user()->id)
            ->where('blocked_user_id', $blockedUserId)
            ->delete();

        return response()->json(['message' => 'Usuário desbloqueado.']);
    }
}