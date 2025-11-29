<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Access tokens - Expiran en 8 horas
        Passport::tokensExpireIn(now()->addHours(8));

        // Refresh tokens - Deben ser más largos que los access tokens (24 horas)
        Passport::refreshTokensExpireIn(now()->addHours(24));

        // Personal access tokens - También 8 horas para consistencia
        Passport::personalAccessTokensExpireIn(now()->addHours(8));
    }
}
