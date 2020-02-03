<?php

namespace Quadram\LaravelPassport\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

const DAY_MINUTES = 60 * 24;
const MONTH_MINUTES = DAY_MINUTES * 30;
const YEAR_MINUTES = MONTH_MINUTES * 12;

class LaravelPassportAuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $tokenExpiresInMinutes = env('TOKEN_EXPIRES_IN_MINUTES', DAY_MINUTES);
        $refreshTokenExpiresInMinutes = env('REFRESH_TOKEN_EXPIRES_IN_MINUTES', MONTH_MINUTES);
        $personalTokenExpiresInMinutes = env('PERSONAL_TOKEN_EXPIRES_IN_MINUTES', YEAR_MINUTES * 100);

        $this->registerPolicies();

        Passport::routes();

        // The token is returned to the client without exchanging an authorization code
        Passport::enableImplicitGrant();

        // Token expires in 1 day
        Passport::tokensExpireIn(now()->addMinutes($tokenExpiresInMinutes));

        // Refresh Token expires in 30 day
        Passport::refreshTokensExpireIn(now()->addMinutes($refreshTokenExpiresInMinutes));

        // Personal Token expires in 100 years
        Passport::personalAccessTokensExpireIn(now()->addMinutes($personalTokenExpiresInMinutes));
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}