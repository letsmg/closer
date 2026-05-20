<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\UserLevel;
use Illuminate\Auth\Access\Response;

/**
 * Policy para autorização baseada em níveis de usuário
 * 
 * Define o que cada nível de usuário pode fazer
 */
class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->canViewAnalytics();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Usuário pode ver próprio perfil
        if ($user->id === $model->id) {
            return true;
        }

        // Admin e Operational podem ver qualquer usuário
        return $user->isAdminLevel();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Qualquer usuário pode criar (registrar) novos usuários
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Usuário pode editar próprio perfil
        if ($user->id === $model->id) {
            return true;
        }

        // Admin pode editar qualquer usuário
        return $user->canManageUsers();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Usuário não pode deletar próprio perfil
        if ($user->id === $model->id) {
            return false;
        }

        // Apenas Admin pode deletar usuários
        return $user->canManageUsers();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->canManageUsers();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->canManageUsers();
    }

    /**
     * Determine whether the user can manage user levels.
     */
    public function manageLevels(User $user): bool
    {
        return $user->canManageUsers();
    }

    /**
     * Determine whether the user can view analytics.
     */
    public function viewAnalytics(User $user): bool
    {
        return $user->canViewAnalytics();
    }

    /**
     * Determine whether the user can moderate content.
     */
    public function moderateContent(User $user): bool
    {
        return $user->canModerateContent();
    }

    /**
     * Determine whether the user can access premium features.
     */
    public function accessPremium(User $user): bool
    {
        return $user->hasPremiumAccess();
    }

    /**
     * Determine whether the user can access plus features.
     */
    public function accessPlus(User $user): bool
    {
        return $user->hasPlusAccess();
    }

    /**
     * Determine whether the user can use shorts.
     */
    public function useShorts(User $user): bool
    {
        return $user->canUseShorts();
    }

    /**
     * Determine whether the user can view likes.
     */
    public function viewLikes(User $user): bool
    {
        return $user->canViewLikes();
    }

    /**
     * Determine whether the user can use advanced filters.
     */
    public function useAdvancedFilters(User $user): bool
    {
        return $user->canUseAdvancedFilters();
    }

    /**
     * Determine whether the user can have verified profile.
     */
    public function haveVerifiedProfile(User $user): bool
    {
        return $user->canHaveVerifiedProfile();
    }

    /**
     * Determine whether the user can bypass rate limits.
     */
    public function bypassRateLimit(User $user): bool
    {
        return $user->isAdminLevel();
    }

    /**
     * Determine whether the user can access admin panel.
     */
    public function accessAdminPanel(User $user): bool
    {
        return $user->isAdminLevel();
    }

    /**
     * Determine whether the user can manage system settings.
     */
    public function manageSettings(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view system logs.
     */
    public function viewLogs(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can export data.
     */
    public function exportData(User $user): bool
    {
        return $user->canViewAnalytics();
    }

    /**
     * Determine whether the user can impersonate other users.
     */
    public function impersonate(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view sensitive location data.
     * 
     * Apenas ADMIN(10) pode ver geolocalização exata.
     * OPERATIONAL(11) e SUPPORT(12) veem apenas dados mascarados.
     */
    public function viewSensitiveLocation(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view payment/financial data.
     * 
     * Apenas ADMIN(10) pode ver dados financeiros.
     * OPERATIONAL(11) e SUPPORT(12) são estritamente bloqueados.
     */
    public function viewFinancialData(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view real-time tracking logs.
     * 
     * Apenas ADMIN(10) pode ver logs de rastreamento em tempo real.
     * OPERATIONAL(11) é bloqueado por questões de privacidade (LGPD/ISO 27001).
     */
    public function viewTrackingLogs(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view raw IP addresses.
     */
    public function viewRawIp(User $user): bool
    {
        return $user->isAdmin();
    }
}
