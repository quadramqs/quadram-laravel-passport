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
    public function user_trait_must_use_default_fields()
    {
        /** @var User $user */
        $user = User::create([
            'name' => 'Test user',
            'email' => 'email@test.com',
            'password' => 'secret',
        ]);

        self::assertEquals('email', $user->getFindForPassportField());
        self::assertEquals($user->password, $user->getValidateForPassportPasswordGrantField());
        self::assertFalse($user->passwordFieldMustBeHashed());
    }

    /** @test */
    public function user_trait_can_use_custom_fields()
    {
        /** @var User $user */
        $user = User::create([
            'name' => 'Test user',
            'email' => 'email@test.com',
            'password' => 'secret',
        ]);

        $user->passportUsername = 'test_name';
        $user->passportPassword = 'passportPassword';
        $user->passportPasswordCheck = true;

        self::assertEquals($user->passportUsername, $user->getFindForPassportField());
        self::assertEquals($user->passportPassword, $user->getValidateForPassportPasswordGrantField());
        self::assertTrue($user->passwordFieldMustBeHashed());
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
}
