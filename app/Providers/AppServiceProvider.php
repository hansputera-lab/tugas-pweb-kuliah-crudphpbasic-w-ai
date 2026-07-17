<?php

namespace App\Providers;

use App\Domains\Settings\Models\Setting;
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
        try {
            $companyName = Setting::where('key', 'company_name')->value('value');
            if ($companyName) {
                config(['app.name' => $companyName]);
            }
        } catch (\Throwable $e) {
            // Table may not exist during setup
        }

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
