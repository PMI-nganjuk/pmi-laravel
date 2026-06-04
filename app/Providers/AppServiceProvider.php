<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Enums\RoleEnum;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

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
        // Implicitly grant "Admin" role all permissions
        Gate::before(function ($user, $ability) {
            return $user->hasRole(RoleEnum::ADMIN) ? true : null;
        });

        // Define gate to restrict user management to Admins only
        Gate::define('manage-users', function ($user) {
            return false; // Normal users are denied; Admins bypass this via Gate::before
        });

        // Define gate to restrict settings management to Admins only
        Gate::define('manage-settings', function ($user) {
            return false; // Normal users are denied; Admins bypass this via Gate::before
        });

        // Define gates for finance module access
        Gate::define('finance.view', function ($user) {
            return $user->hasAnyRole([
                RoleEnum::FINANCIAL_MANAGER,
                RoleEnum::FINANCE_STAFF,
            ]);
        });

        Gate::define('finance.create', function ($user) {
            return $user->hasAnyRole([
                RoleEnum::FINANCIAL_MANAGER,
                RoleEnum::FINANCE_STAFF,
            ]);
        });

        // Define a strict rate limiter for login requests (5 attempts per minute per IP)
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });
    }
}
