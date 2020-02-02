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
//        if (! $this->app->configurationIsCached()) {
//            $this->mergeConfigFrom(__DIR__.'/../config/passport.php', 'passport');
//        }
//        $this->publishes([
//            __DIR__ . '/../../src/config/laravelpassport.php' => config_path('laravelpassport.php'),
//        ]);
    }

    /**
     * Merge the given configuration with the existing configuration.
     *
     * @param  string  $path
     * @param  string  $key
     * @return void
     */
//    protected function mergeConfigFrom($path, $key)
//    {
//        if (! $this->app->configurationIsCached()) {
//            $this->app['config']->set($key, array_merge(
//                require $path, $this->app['config']->get($key, [])
//            ));
//        }
//    }
}
