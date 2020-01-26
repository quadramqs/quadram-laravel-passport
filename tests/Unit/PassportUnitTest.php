<?php

namespace Quadram\LaravelPassport\Test\Unit;

use Laravel\Passport\Client;
use Quadram\LaravelPassport\Test\TestCase;

class PassportUnitTest extends TestCase
{

    /** @test */
    public function migrations_must_be_created_after_install_command()
    {
        $this->assertEquals(2, Client::count());
    }

    /** @test */
    public function function_must_return_first_password_client()
    {
        $passportClient = Client::where('password_client', true)->first();

        $this->assertEquals(2, $passportClient->id);
    }

}