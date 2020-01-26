<?php


namespace Quadram\LaravelPassport\Traits;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;

trait LaravelPassportTrait
{
    use HasApiTokens, Notifiable;

    public $authorization;

    /**
     * Find the user instance for the given username.
     *
     * @param string $username
     * @return \App\User
     */
    public function findForPassport($username)
    {
        return $this->where('username', $username)->first();
    }

    /**
     * Validate the password of the user for the Passport password grant.
     *
     * @param string $password
     * @return bool
     */
    public function validateForPassportPasswordGrant($password)
    {
        return Hash::check($password, $this->password);
    }

    /**
     * @return mixed
     */
    public function createExpiringToken()
    {
        $http = new Client;

        $passportClient = \Laravel\Passport\Client::where('password', true)->first();

        $response = $http->post(url('oauth/token'), [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => $passportClient->id,
                'client_secret' => $passportClient->secret,
                'username' => 'username',
                'password' => 'my-password',
                'scope' => '',
            ],
        ]);

        return json_decode((string)$response->getBody(), true);
    }
}