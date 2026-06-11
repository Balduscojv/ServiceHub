<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // manager e admin têm acesso total; retornar true no before interrompe avaliação
        Gate::before(function (User $user, string $ability) {
            if ($user->relationLoaded('roles') === false) {
                $user->load('roles.permissions');
            }
            if ($user->hasRole('manager') || $user->hasRole('admin')) {
                return true;
            }
        });

        Gate::after(function (User $user, string $ability) {
            return $user->hasPermission($ability) ?: null;
        });
    }
}
