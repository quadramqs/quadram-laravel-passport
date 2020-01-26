<?php

namespace Quadram\LaravelPassport\Test\Unit;

use Laravel\Passport\Client;
use Quadram\LaravelPassport\Test\TestCase;

class PassportUnitTest extends TestCase
{

    /** @test */
    public function migrations_must_be_created_after_install_command()
    {
        $this->artisan('quadram:laravel-passport-install');

        $this->assertEquals(2, Client::count());
    }

}