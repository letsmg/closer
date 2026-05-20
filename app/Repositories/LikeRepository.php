<?php

namespace App\Repositories;

use App\Models\LikeModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * Repositório para operações de persistência e consulta de likes.
 * 
 * Centraliza consultas relacionadas a curtidas entre perfis,
 * incluindo limites diários e verificação de matches.
 */
class LikeRepository
{
    /**
     * Registra um like
     */
    public function create(array $data): LikeModel
    {
        return LikeModel::create($data);
    }

    /**
     * Verifica se já existe like entre dois usuários
     */
    public function existsLike(int $fromUserId, int $toUserId): bool
    {
        return LikeModel::where('from_user_id', $fromUserId)
            ->where('to_user_id', $toUserId)
            ->exists();
    }

    /**
     * Verifica se há like recíproco (match)
     */
    public function existsMutualLike(int $userA, int $userB): bool
    {
        return LikeModel::where('from_user_id', $userA)
            ->where('to_user_id', $userB)
            ->where('is_like', true)
            ->exists() &&
            LikeModel::where('from_user_id', $userB)
            ->where('to_user_id', $userA)
            ->where('is_like', true)
            ->exists();
    }

    /**
     * Conta likes dados hoje por um usuário
     */
    public function countTodayLikes(int $userId): int
    {
        return LikeModel::where('from_user_id', $userId)
            ->whereDate('created_at', today())
            ->count();
    }

    /**
     * Retorna quem deu like no usuário (para view de likes recebidos)
     */
    public function findWhoLikedMe(int $userId): Collection
    {
        return LikeModel::where('to_user_id', $userId)
            ->where('is_like', true)
            ->with('fromUser.profile')
            ->get();
    }

    /**
     * Retorna likes dados pelo usuário
     */
    public function findLikesByUser(int $userId): Collection
    {
        return LikeModel::where('from_user_id', $userId)
            ->with('toUser.profile')
            ->get();
    }

    /**
     * Remove um like (dislike/unmatch)
     */
    public function delete(int $fromUserId, int $toUserId): bool
    {
        return LikeModel::where('from_user_id', $fromUserId)
            ->where('to_user_id', $toUserId)
            ->delete() > 0;
    }

    /**
     * Conta total de likes recebidos por um usuário
     */
    public function countLikesReceived(int $userId): int
    {
        return LikeModel::where('to_user_id', $userId)
            ->where('is_like', true)
            ->count();
    }
}
