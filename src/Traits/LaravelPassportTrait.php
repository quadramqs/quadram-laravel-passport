<?php

namespace Quadram\LaravelPassport\Traits;

use GuzzleHttp\Client;
use Illuminate\Foundation\Auth\User;
use Laravel\Passport\HasApiTokens;

trait LaravelPassportTrait
{
    use HasApiTokens;

    public $authorization = [];

    /**
     * Find the user instance for the given username.
     *
     * @param int $id
     * @return User
     */
    public function findForPassport($id)
    {
        return $this->find($id);
    }

    /**
     * Auto validates the user cause we already have an instance of the user
     *
     * @param string $password
     * @return bool
     */
    public function validateForPassportPasswordGrant($password)
    {
        return true;
    }

    /**
     * Return the Passport client
     *
     * @param array $params
     * @return mixed
     */
    public function getClient($params = ['password_client' => true])
    {
        return \Laravel\Passport\Client::where($params)->first();
    }

    /**
     * Make a post call to oauth/token endpoint and save the authorization in the user instance
     *
     * @param $params
     */
    public function postAuthorization($params)
    {
        $passportClient = $this->getClient();

        $clientParams = [
            'client_id' => $passportClient->id,
            'client_secret' => $passportClient->secret
        ];

        $http = new Client;

        $response = $http->post(url('oauth/token'), [
            'form_params' => array_merge($clientParams, $params)
        ]);

        $this->setAuthorization(json_decode((string)$response->getBody(), true));
    }

    /**
     * Update the user instance with an authorization array
     *
     * @param $authorization
     */
    public function setAuthorization($authorization)
    {
        $this->authorization = [
            'accessToken' => $authorization->access_token ?? $authorization->accessToken ?? null,
            'refreshToken' => $authorization->refresh_token ?? null,
            'expires' => $authorization->expires_in ?? null,
        ];
    }

    /**
     * Create a new expiring token for the current user
     *
     * @param string $scope
     */
    public function createAccessToken($scope = '*')
    {
        $this->postAuthorization([
            'grant_type' => 'password',
            'username' => $this->id,
            'password' => null,
            'scope' => $scope,
        ]);
    }

    /**
     * Creates a new expiring token using the user's refresh token.
     *
     * @param $refreshToken
     */
    public function refreshToken($refreshToken)
    {
        $this->postAuthorization([
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
        ]);
    }

    /**
     * Creates a new personal access token with the default expiration date
     *
     * @param string $name
     */
    public function createPersonalToken($name = 'Personal Access Token')
    {
        $jwt = $this->createToken($name);

        $this->setAuthorization($jwt);
    }

}
