<?php

namespace Quadram\LaravelPassport\Traits;

use GuzzleHttp\Client;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Laravel\Passport\HasApiTokens;
use Laravel\Passport\Token;
use Lcobucci\JWT\Parser;

trait LaravelPassportTrait
{
    use HasApiTokens;

    protected $authorization = [];

    public function getAuthorizationAttribute()
    {
        return $this->authorization;
    }

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
        if (env('APP_ENV') === 'testing') {
            $this->setTestingAuthorization();
            return;
        }

        $passportClient = $this->getClient();

        $clientParams = [
            'client_id' => $passportClient->id,
            'client_secret' => $passportClient->secret
        ];

        $request = Request::create(url('oauth/token'), 'POST', array_merge($clientParams, $params));

        $response = app()->handle($request);

        $this->setAuthorization(json_decode($response->getContent()));
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

        $this->append('authorization');
    }

    /**
     * Update the user instance with an authorization array for testing
     */
    public function setTestingAuthorization()
    {
        $jwt = [
            'accessToken' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIyIiwianRpIjoiMjg4ZDcwN2JhZWY4ZTViNmE1YTU1NTZjZDVmODYwMzE3NmVkNjcyZjkzMmNiNTlmYjM1YTU3NGIxMmE2Y2U3OGM2OTY5MDBlYWM3NjhhNDkiLCJpYXQiOjE1ODYwMjAxODUsIm5iZiI6MTU4NjAyMDE4NSwiZXhwIjoxNTg2MTA2NTg1LCJzdWIiOiIxMzAiLCJzY29wZXMiOlsiKiJdfQ.v6-Pggm2hyTLDul910ZPhvySCI-DY-F4JAFQB_7Nj0LqF491kLIhaeAjANpu5saPbCrnE8jzkVBDM9HOKay7YGF7-2Ntl-BzSSIzAuGJB1MTL8RnYDmjPXO4V6EDiTFsgX1NgdJ0xXD2YWkpjwlOU8GaGbjBRG0XqwyfTHJCxICihRRuidvHBrtJAZpqDB1OiOy63doRNC3kWcwo4C0qzmdknfSyr-YvW4OYvPPURwu9IneTqUBhqFvLm9YMiuKrPYwQTSniIMBQIU1NJp22DjgoMs62Ml8BB4RpiZX8tPmIY1PN4y6M4MJrd0Vy44fjpfscMZQiSdPk6vTpVqFSd_2ElrJeLn5Z2jAGo4yAvbnHWBdHRcpQ51aGaSOwLTa0iUCinRAUSYNhrt1OFEUgA77Wsyq-GabMSgeIDO_C86PdMKG5x3TOhe6i6wxI0SexzmFOPeDt0NjWrCL1uLSmizoN1CtPJ3L3EvXOUp5bZEa14mM8gKmpRmZktrL56XwOYAMWL-cc-cjJxlrl4LUFm_p1-2je3JYCQdOhQMjymP_S6Wrv5dgpUXmVysTHzyo_ygOUpvGrJuO4zNRTvZpziPIdQ897_UgTxo1pb5LPnIx0wc-_37gbHhdHBpIwRNXb1Tg4RqdLvnEbfJxlKp2ZBsnsly1PXxn73f6_xTVKvdo',
            'refreshToken' => 'def502004868c06288b34a1f686a2ed385fbc51e8c7b14c39e968e3f65b3cfcf1b4ab7484f256058e98310152e422694c66eaa6207eafc5dba564ac91c809055d05c9a7cfc520365c1e865308bea4cee1e606da3c4b4fbd28473b2ac1b02ff6eb57d8bac7c49c98f81aa10148b0493b229cae778b1ede7ff1712dcf927d7137b1b91a7c2daa1e7cc6947a0fd2f40a448f5a2d2859ebaa546848931ea56bddbbe8583e11676f3a190f3e018544dfa363c220807bb386d292874d47863884beed5cd8047410be31a8bfd6799eb7ff6b36c971c226b040492b37248467bf253599a62ead4045e3f6dc3af5eaaffe62fe6d6be3d83e0c54b9b9b80efd12e5f498b26edcca06456fa63df521b3be7338a1d03132741f07e144d97b0f1cb65fc34e93c4ff629f7a52561fcd6e21c1966d8cf6d1c965db8e7c3a380a8244ad6bfbf097d73ff0e01277c9858c0916ca8e27b2991d7fb70068431655add493cb4efc062d075244270438c',
            'expires' => 86400,
        ];

        $this->setAuthorization((object)$jwt);
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
            'password' => $this->password,
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

    /**
     * Get the token related with the Authorization header request
     *
     * @return mixed
     */
    public static function getTokenUsingBearer()
    {
        $bearerToken = request()->bearerToken();

        $tokenId = (new Parser())->parse($bearerToken)->getClaim('jti');

        return Token::find($tokenId);
    }

    /**
     * Return the user using the Authorization header bearer from request
     *
     * @return null|\App\User
     */
    public static function byToken()
    {
        if (env('APP_ENV') === 'testing') {
            return auth()->user();
        }

        if (!$token = self::getTokenUsingBearer()) {
            return null;
        }

        return static::find($token->user_id);
    }

    /**
     * Revoke the token using the Authorization header request
     */
    public function revokeRequestedAccessToken()
    {
        if (!$token = self::getTokenUsingBearer()) {
            return null;
        }

        $token->revoke();
    }

}
