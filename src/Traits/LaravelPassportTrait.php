<?php


namespace Quadram\LaravelPassport\Traits;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

trait LaravelPassportTrait
{
    use HasApiTokens;

    public $authorization;

    /**
     * @return string
     */
    public function getFindForPassportField()
    {
        return $this->passportUsername ?? 'email';
    }

    /**
     * @return mixed
     */
    public function getValidateForPassportPasswordGrantField()
    {
        $passwordField = $this->passportPassword ?? 'password';

        return $this->{$passwordField};
    }

    /**
     * @return bool
     */
    public function passwordFieldMustBeHashed()
    {
        return !!$this->passportPasswordCheck;
    }

    /**
     * Find the user instance for the given username.
     *
     * @param string $username
     * @return \App\User
     */
    public function findForPassport($username)
    {
        return $this->where($this->getFindForPassportField(), $username)->first();
    }

    /**
     * Validate the password of the user for the Passport password grant.
     *
     * @param string $password
     * @return bool
     */
    public function validateForPassportPasswordGrant($password)
    {
        if(!!$this->passwordFieldMustBeHashed()) {
            return Hash::check($password, $this->getValidateForPassportPasswordGrantField());
        }

        return $password === $this->getValidateForPassportPasswordGrantField();
    }

    public function getClient($params = ['password_client' => true])
    {
        return \Laravel\Passport\Client::where($params)->first();
    }

    /**
     * @return mixed
     */
    public function createExpiringToken()
    {
        $http = new Client;

        $passportClient = $this->getClient();

        $response = $http->post(url('oauth/token'), [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => $passportClient->id,
                'client_secret' => $passportClient->secret,
                'username' => $this->email,
                'password' => $this->password,
                'scope' => '',
            ],
        ]);

        return json_decode((string)$response->getBody(), true);
    }

    /**
     * Creates a new expiring token using the user refresh token.
     *
     * @return mixed
     */
    public function refreshToken()
    {
        $http = new Client;

        $passportClient = $this->getClient();

        $response = $http->post(url('oauth/token'), [
            'form_params' => [
                'grant_type' => 'refresh_token',
                'client_id' => $passportClient->id,
                'client_secret' => $passportClient->secret,
                'refresh_token' => request()->header('refresh_token'),
            ],
        ]);

        return json_decode((string)$response->getBody(), true);
    }

}
