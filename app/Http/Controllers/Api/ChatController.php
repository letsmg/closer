<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserMatch;
use App\Models\Message;
use App\Models\Block;
use App\Models\User;
use App\Repositories\MessageRepository;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function __construct(
        private MessageRepository $messageRepository
    ) {}

    /**
     * Exibe as mensagens de um match específico
     */
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
        
        // Verificação de Bloqueio
        $bloqueado = Block::where(function($q) use ($user, $parceiroId) {
            $q->whereHas('profile', fn($p) => $p->where('user_id', $user->id))
              ->whereHas('blockedProfile', fn($p) => $p->where('user_id', $parceiroId));
        })->orWhere(function($q) use ($user, $parceiroId) {
            $q->whereHas('profile', fn($p) => $p->where('user_id', $parceiroId))
              ->whereHas('blockedProfile', fn($p) => $p->where('user_id', $user->id));
        })->exists();

        if ($bloqueado) {
            return response()->json(['error' => 'Este chat não está mais disponível.'], 403);
        }

        $parceiro = User::with(['profile.photos'])->find($parceiroId);

        // 3. Trava de limite para FREE(0) - máximo de novos matches por dia
        if ($user->nivel_acesso == 0) {
            $meusChatsHoje = UserMatch::where(function($q) use ($user) {
                    $q->where('user_one_id', $user->id)->orWhere('user_two_id', $user->id);
                })
                ->where('created_at', '>=', now()->startOfDay())
                ->count();

            if ($meusChatsHoje > 20) {
                return response()->json(['error' => 'Limite de novos chats atingido.'], 403);
            }
        }

        // Marcar mensagens do parceiro como lidas ao abrir o chat
        Message::where('user_match_id', $match->id)
            ->where('sender_id', $parceiroId)
            ->update(['read' => true]);

        // 4. Buscar mensagens
        $mensagens = Message::where('user_match_id', $match->id)
            ->orderBy('created_at', 'asc')
            ->paginate(50);

        return response()->json([
            'parceiro' => [
                'id' => $parceiro->id,
                'nome' => $parceiro->name,
                'foto' => $parceiro->profile?->photos?->where('is_principal', true)->first()?->path,
            ],
            'mensagens' => $mensagens
        ]);
    }

    /**
     * Envia uma mensagem em um match existente
     */
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

        // 🔒 Verifica limite de mensagens sem match para PLUS(2)
        // PLUS pode enviar até 10 mensagens/dia para perfis sem match ativo
        if ($user->nivel_acesso == 2) {
            $dailyMessages = $this->messageRepository->countTodayMessages($user->id);
            $limit = $user->getLevelAttribute()->getDailyMessagesLimit();
            
            if ($dailyMessages >= $limit) {
                return response()->json([
                    'error' => 'Limite diário de mensagens sem match atingido.',
                    'message' => "Seu plano Plus permite até {$limit} mensagens por dia para perfis sem match."
                ], 403);
            }
        }

        // Salvar a Mensagem
        $mensagem = Message::create([
            'user_match_id' => $match->id,
            'sender_id'     => $user->id,
            'content'       => $request->conteudo,
            'read'          => false
        ]);

        // Atualizar o timestamp do match (para ele subir na lista de conversas)
        $match->touch(); 

        return response()->json([
            'status' => 'sucesso',
            'mensagem' => $mensagem
        ], 201);
    }

    /**
     * Envia mensagem para um usuário sem match ativo (apenas PLUS+)
     */
    public function enviarMensagemDireta(Request $request)
    {
        $request->validate([
            'to_user_id' => 'required|exists:users,id',
            'conteudo' => 'required|string|max:1000',
        ]);

        $user = $request->user();
        $targetId = $request->to_user_id;

        // 🔒 Verifica permissão: apenas PLUS(2)+ pode enviar mensagem sem match
        if (!$user->hasPlusAccess()) {
            return response()->json([
                'error' => 'Seu plano não permite enviar mensagens sem um match ativo.',
            ], 403);
        }

        // 🔒 Verifica limite diário para PLUS(2)
        if ($user->nivel_acesso == 2) {
            $dailyMessages = $this->messageRepository->countTodayMessages($user->id);
            $limit = $user->getLevelAttribute()->getDailyMessagesLimit();
            
            if ($dailyMessages >= $limit) {
                return response()->json([
                    'error' => 'Limite diário de mensagens sem match atingido.',
                    'message' => "Seu plano Plus permite até {$limit} mensagens por dia para perfis sem match."
                ], 403);
            }
        }

        // Cria um match temporário para agrupar as mensagens
        $match = UserMatch::firstOrCreate([
            'user_one_id' => $user->id,
            'user_two_id' => $targetId,
        ]);

        $mensagem = Message::create([
            'user_match_id' => $match->id,
            'sender_id'     => $user->id,
            'content'       => $request->conteudo,
            'read'          => false
        ]);

        return response()->json([
            'status' => 'sucesso',
            'mensagem' => $mensagem
        ], 201);
    }
}
