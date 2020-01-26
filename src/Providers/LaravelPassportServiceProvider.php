<?php

namespace Quadram\LaravelPassport\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\PassportServiceProvider;
use Quadram\LaravelPassport\Console\InstallCommand;

class LaravelPassportServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        App::register(PassportServiceProvider::class);

        $this->commands([
            InstallCommand::class
        ]);
    }


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->publishes([
            __DIR__ . '/../../src/config/laravelpassport.php' => config_path('laravelpassport.php'),
        ]);
    }
}