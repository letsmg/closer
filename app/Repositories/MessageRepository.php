<?php

namespace App\Repositories;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * Repositório para operações de persistência e consulta de mensagens.
 * 
 * Centraliza a lógica de acesso a dados do chat,
 * incluindo limites de envio por plano e verificação de match.
 */
class MessageRepository
{
    /**
     * Envia uma nova mensagem
     */
    public function create(array $data): Message
    {
        return Message::create($data);
    }

    /**
     * Busca mensagens entre dois usuários
     */
    public function findConversation(int $userA, int $userB, int $limit = 50): Collection
    {
        return Message::where(function ($query) use ($userA, $userB) {
            $query->where('from_user_id', $userA)
                ->where('to_user_id', $userB);
        })->orWhere(function ($query) use ($userA, $userB) {
            $query->where('from_user_id', $userB)
                ->where('to_user_id', $userA);
        })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse();
    }

    /**
     * Conta mensagens enviadas hoje por um usuário
     */
    public function countTodayMessages(int $userId): int
    {
        return Message::where('from_user_id', $userId)
            ->whereDate('created_at', today())
            ->count();
    }

    /**
     * Conta mensagens enviadas hoje para um destinatário específico (sem match)
     */
    public function countTodayMessagesToUser(int $fromUserId, int $toUserId): int
    {
        return Message::where('from_user_id', $fromUserId)
            ->where('to_user_id', $toUserId)
            ->whereDate('created_at', today())
            ->count();
    }

    /**
     * Busca as últimas conversas do usuário
     */
    public function findRecentConversations(int $userId): Collection
    {
        $subquery = Message::selectRaw('MAX(id) as id')
            ->where(function ($q) use ($userId) {
                $q->where('from_user_id', $userId)
                    ->orWhere('to_user_id', $userId);
            })
            ->groupByRaw('CASE WHEN from_user_id = ? THEN to_user_id ELSE from_user_id END', [$userId]);

        return Message::whereIn('id', $subquery)
            ->with(['fromUser.profile', 'toUser.profile'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Marca mensagens como lidas
     */
    public function markAsRead(int $userId, int $fromUserId): int
    {
        return Message::where('to_user_id', $userId)
            ->where('from_user_id', $fromUserId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Conta mensagens não lidas
     */
    public function countUnread(int $userId): int
    {
        return Message::where('to_user_id', $userId)
            ->whereNull('read_at')
            ->count();
    }
}
