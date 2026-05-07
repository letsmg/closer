<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Define Gates para controle de acesso administrativo
        Gate::define('access-admin-panel', function (User $user) {
            return $user->isAdminLevel();
        });

        Gate::define('view-analytics', function (User $user) {
            return $user->canViewAnalytics();
        });
    }
}
