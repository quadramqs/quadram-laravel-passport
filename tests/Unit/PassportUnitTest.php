<?php

namespace Quadram\LaravelPassport\Test\Unit;

use Illuminate\Contracts\Routing\Registrar;
use Laminas\Diactoros\Response as Psr7Response;
use Laravel\Passport\Client;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Laravel\Passport\Http\Middleware\CheckClientCredentials;
use Laravel\Passport\Passport;
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
    public function user_with_trait_can_generate_a_token()
    {
        /** @var User $user */
        $user = User::create([
            'name' => 'Test user',
            'email' => 'email@test.com',
            'password' => 'secret'
        ]);

        $this->assertNotNull($user);

//        $user->createExpiringToken();
//        $this->assertNotNull($user->token);


        $this->withoutExceptionHandling();

        /** @var Registrar $router */

        $response = $this->get('/oauth');

        $this->assertNotNull($response);

        dd($response);

        Passport::actingAsClient(new Client());

        $response = $this->get('/foo');
        $response->assertSuccessful();
        $response->assertSee('bar');
    }
}
