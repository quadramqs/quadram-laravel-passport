<?php

namespace Quadram\LaravelPassport\Test;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Passport\PassportServiceProvider;
use Quadram\LaravelPassport\Providers\LaravelPassportServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    use DatabaseMigrations;

    protected $setUpHasRunOnce = false;

    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        if (!$this->setUpHasRunOnce) {
            $this->clearCache();

            $this->runMigrations();

            $this->setUpHasRunOnce = true;
        }
    }

    public function runMigrations()
    {
        // Run initial migrations
        $this->artisan('migrate', ['--database' => 'testbench','--path'=>'migrations']);

        // Publish config files
//        $this->artisan('vendor:publish', ['--provider' => LaravelPassportServiceProvider::class]);

        // Publish Passport Migrations
        // $this->artisan('vendor:publish', ['--tag' => 'passport-migrations']);

        // Install Passport
        $this->artisan('quadram:laravel-passport-install');
    }

    public function clearCache()
    {
        $this->artisan('config:clear');
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
        return [
            LaravelPassportServiceProvider::class,
            PassportServiceProvider::class,
        ];
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

    protected function tearDown(): void
    {
        $refl = new \ReflectionObject($this);
        foreach ($refl->getProperties() as $prop) {
            if (!$prop->isStatic() && 0 !== strpos($prop->getDeclaringClass()->getName(), 'PHPUnit_')) {
                $prop->setAccessible(true);
                $prop->setValue($this, null);
            }
        }
    }

    protected function getBasePath()
    {
        return __DIR__.'/../vendor/orchestra/testbench-core/laravel';
    }
}
