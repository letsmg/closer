<?php

namespace App\Repositories;

use App\Models\User;
use App\Enums\UserLevel;
use Illuminate\Database\Eloquent\Collection;

/**
 * Repositório para operações de persistência e consulta de usuários.
 * 
 * Encapsula toda a lógica de acesso a dados da entidade User,
 * mantendo os controllers enxutos e a lógica de negócio nos Services.
 */
class UserRepository
{
    /**
     * Busca usuário por ID
     */
    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    /**
     * Busca usuário por UUID
     */
    public function findByUuid(string $uuid): ?User
    {
        return User::where('uuid', $uuid)->first();
    }

    /**
     * Busca usuário por email
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Retorna todos os usuários de um nível específico
     */
    public function findByLevel(UserLevel $level): Collection
    {
        return User::where('nivel_acesso', $level->value)->get();
    }

    /**
     * Retorna usuários com nível acima de um valor
     */
    public function findByLevelAbove(int $minLevel): Collection
    {
        return User::where('nivel_acesso', '>=', $minLevel)->get();
    }

    /**
     * Retorna usuários com nível abaixo de um valor (consumers)
     */
    public function findByLevelBelow(int $maxLevel): Collection
    {
        return User::where('nivel_acesso', '<', $maxLevel)->get();
    }

    /**
     * Cria um novo usuário
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * Atualiza um usuário
     */
    public function update(User $user, array $data): bool
    {
        return $user->update($data);
    }

    /**
     * Remove um usuário
     */
    public function delete(User $user): bool
    {
        return $user->delete();
    }

    /**
     * Incrementa o contador de likes diários
     */
    public function incrementDailyLikes(User $user): void
    {
        $user->increment('daily_likes_count');
    }

    /**
     * Reseta o contador de likes diários para todos os usuários
     */
    public function resetDailyLikes(): int
    {
        return User::query()->update(['daily_likes_count' => 0]);
    }

    /**
     * Busca usuários ativos (online nos últimos N minutos)
     */
    public function findActive(int $minutes = 15): Collection
    {
        return User::where('last_active_at', '>=', now()->subMinutes($minutes))->get();
    }
}
