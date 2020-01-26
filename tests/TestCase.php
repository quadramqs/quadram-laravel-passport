<?php

namespace Quadram\LaravelPassport\Test;

use Quadram\LaravelPassport\Providers\LaravelPassportServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{

    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->clearCache();

        // Run initial migrations
        $this->artisan('migrate',
            ['--database' => 'testbench'])->run();

        // Publish config files
        $this->artisan('vendor:publish', ['--provider' => LaravelPassportServiceProvider::class]);

        // Publish Passport Migrations
        $this->artisan('vendor:publish', ['--tag' => 'passport-migrations']);
    }

    public function clearCache()
    {
        foreach (glob(__DIR__ . '/../bootstrap/cache{,t}/*.php', GLOB_BRACE) as $file) {
            unlink($file);
        }
    }

    /**
     * add the package provider
     *
     * @param $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [LaravelPassportServiceProvider::class];
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}