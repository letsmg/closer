<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserMatch;
use App\Models\Mensagem;
use App\Models\Bloqueio;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function show($matchId, Request $request)
    {
        $user = $request->user();
        
        // 1. Verificar se o match existe e pertence ao usuário
        $match = UserMatch::where('id', $matchId)
            ->where(function($q) use ($user) {
                $q->where('user_one_id', $user->id)
                  ->orWhere('user_two_id', $user->id);
            })->firstOrFail();

        // 2. Identificar o parceiro
        $parceiroId = ($match->user_one_id == $user->id) ? $match->user_two_id : $match->user_one_id;
        
        // Verificação de Bloqueio: Se houver bloqueio, o chat não deve abrir
        $bloqueado = Bloqueio::where(function($q) use ($user, $parceiroId) {
            $q->where('user_id', $user->id)->where('blocked_user_id', $parceiroId);
        })->orWhere(function($q) use ($user, $parceiroId) {
            $q->where('user_id', $parceiroId)->where('blocked_user_id', $user->id);
        })->exists();

        if ($bloqueado) {
            return response()->json(['error' => 'Este chat não está mais disponível.'], 403);
        }

        $parceiro = \App\Models\User::with(['fotos'])->find($parceiroId);

        // 3. Trava de limite Nível 0 (Otimizada)
        if ($user->nivel_acesso == 0) {
            $meusChatsHoje = UserMatch::where(function($q) use ($user) {
                    $q->where('user_one_id', $user->id)->orWhere('user_two_id', $user->id);
                })
                ->where('created_at', '>=', now()->startOfDay()) // Mais preciso que subDay()
                ->count();

            if ($meusChatsHoje > 20) {
                return response()->json(['error' => 'Limite de novos chats atingido.'], 403);
            }
        }

        // Marcar mensagens do parceiro como lidas ao abrir o chat
        $match->mensagens()->where('sender_id', $parceiroId)->update(['lida' => true]);

        // 4. Preparar o "Pacote" para o Kotlin
        return response()->json([
            'parceiro' => [
                'id' => $parceiro->id,
                'nome' => $parceiro->name,
                'foto' => $parceiro->fotos->where('is_principal', true)->first()?->path, // Usando path conforme padrão anterior
                'shorts_pergunta' => $parceiro->shorts()->where('tipo', 'q')->orderBy('posicao')->get(),
            ],
            'meus_shorts_resposta' => $user->shorts()->where('tipo', 'r')->orderBy('posicao')->get(),
            'mensagens' => $match->mensagens()->oldest()->paginate(50) // oldest() costuma ser melhor para chat
        ]);
    }

    public function enviarMensagem(Request $request, $matchId)
    {
        $request->validate([
            'conteudo' => 'required|string|max:1000',
        ]);

        $user = $request->user();

        $match = UserMatch::where('id', $matchId)
            ->where(function($q) use ($user) {
                $q->where('user_one_id', $user->id)
                  ->orWhere('user_two_id', $user->id);
            })->firstOrFail();

        // 2. Salvar a Mensagem
        $mensagem = Mensagem::create([
            'user_match_id' => $match->id,
            'sender_id'     => $user->id,
            'conteudo'      => $request->conteudo,
            'lida'          => false
        ]);

        // 3. Atualizar o timestamp do match (para ele subir na lista de conversas)
        $match->touch(); 

        return response()->json([
            'status' => 'sucesso',
            'mensagem' => $mensagem
        ], 201);
    }
}