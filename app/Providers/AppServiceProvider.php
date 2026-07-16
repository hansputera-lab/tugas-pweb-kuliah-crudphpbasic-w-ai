<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            if ($user->role === 'super_admin') {
                return true;
            }

            if ($user->hasPermissionTo($ability)) {
                return true;
            }

            return null;
        });
    }
}
