<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\UserLevel;

/**
 * Policy para operações de bloqueio entre usuários.
 * 
 * Regras de negócio:
 * - MODERATOR(1) pode bloquear customers abaixo de COFOUNDER(4)
 * - COFOUNDER(4)+ e STAFF podem bloquear qualquer customer
 * - Ninguém pode bloquear STAFF (ADMIN, OPERATIONAL, SUPPORT)
 * - Usuários FREE(0) só podem bloquear outros FREE(0)
 */
class BlockPolicy
{
    /**
     * Determina se o usuário pode bloquear outro perfil
     */
    public function block(User $blocker, User $target): bool
    {
        $blockerLevel = $blocker->getLevelAttribute();
        $targetLevel = $target->getLevelAttribute();

        // STAFF não pode ser bloqueado por customers
        if ($targetLevel->isStaff()) {
            return false;
        }

        // STAFF pode bloquear qualquer customer
        if ($blockerLevel->isStaff()) {
            return true;
        }

        // MODERATOR(1): pode bloquear customers abaixo de COFOUNDER(4)
        if ($blockerLevel === UserLevel::MODERATOR) {
            return $targetLevel->value < UserLevel::COFOUNDER->value;
        }

        // COFOUNDER(4)+ e ELITE(5): podem bloquear qualquer customer
        if ($blockerLevel->value >= UserLevel::COFOUNDER->value) {
            return $targetLevel->isConsumer();
        }

        // PLUS(2) e PREMIUM(3): podem bloquear qualquer customer
        if ($blockerLevel->value >= UserLevel::PLUS->value) {
            return $targetLevel->isConsumer();
        }

        // FREE(0): só pode bloquear outros FREE(0)
        return $targetLevel === UserLevel::FREE;
    }
}
