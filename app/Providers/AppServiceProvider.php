<?php

namespace App\Providers;

use App\Scopes\DoctorScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        // Apply DoctorScope across all Eloquent models.
        // The scope itself only activates for doctor users and only for tables
        // that contain ProviderID/DoctorID columns.
        Model::addGlobalScope(new DoctorScope());

        Gate::define('viewAny-dashboard', function ($user) {
            if (! $user) {
                return false;
            }

            if (method_exists($user, 'hasRole')) {
                if ($user->hasRole('Administrator') || $user->hasRole('administrator')) {
                    return true;
                }
            }

            $roleName = $user->RoleName ?? null;

            return is_string($roleName) && strtolower($roleName) === 'administrator';
        });

    }
}
