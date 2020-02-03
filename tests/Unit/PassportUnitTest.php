<?php

namespace Quadram\LaravelPassport\Test\Unit;

use Laravel\Passport\Client;
use Quadram\LaravelPassport\Models\User;
use Quadram\LaravelPassport\Test\TestCase;

class PassportUnitTest extends TestCase
{

    /** @test */
    public function clients_must_be_created()
    {
        $this->assertNotNull(Client::where('password_client', true)->first());
        $this->assertNotNull(Client::where('personal_access_client', true)->first());
    }

    /** @test */
    public function must_return_first_password_client()
    {
        /** @var User $user */
        $user = User::create([
            'name' => 'Test user',
            'email' => 'email@test.com',
            'password' => 'secret',
        ]);

        self::assertNotNull($user->getClient());
    }

    /** @test */
    public function must_create_personal_access_token()
    {
        /** @var User $user */
        $user = User::create([
            'name' => 'Test user',
            'email' => 'email@test.com',
            'password' => 'secret',
        ]);

        $user->createPersonalToken();

        self::assertNotNull($user->authorization['accessToken']);
    }
}
