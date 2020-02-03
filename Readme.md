<p><img src="https://www.quadram.mobi/img/quadram-desarrollo-aplicaciones-moviles-android-iphone-2.png" width="200"></p>

[visit our website](https://www.quadram.mobi/)

## Laravel Passport

This library install Laravel Passport library and allow to issue access tokens for authenticated users. There's no need to redirect your users to the oauth routes, just use the current user instance to get a fresh access token or refresh it.

## Install the project

    # require the library into your project
    composer require quadram/laravel-passport
    
    # install and configure library
    php artisan quadram:laravel-passport
    
## How to use

Add the LaravelPassportTrait to your user model:
    
    use Quadram\LaravelPassport\Traits\LaravelPassportTrait;
    
    class User extends Model
    {
        use LaravelPassportTrait, Authenticatable;
        ...
    
And use your user instance to issue a new access token for the user:

    # access token
    $this->createAccessToken();

    # personal access token
    $this->createPersonalToken();
    

These calls add a new authorization field to the user instance with this structure:
    
        accessToken = [
            'accessToken' => '....',
            'refreshToken' => '....',
            'expiresIn' => 123456...
        ];
    
*** remember to make visible this authorization field and send it along your user instance. 
    
## Environment values

The default expiration times for each token can be configured using the .env file.

    # Default expiration time for access tokens in minutes, default = 60 * 24 -> 1 day
    # TOKEN_EXPIRES_IN_MINUTES
    
    # Default expiration time for refresh tokens in minutes, default = 60 * 24 * 30 -> 30 days
    # REFRESH_TOKEN_EXPIRES_IN_MINUTES
    
    # Default expiration time for personal tokens in minutes, default = 60 * 24 * 30 * 12 * 100 -> 1 year
    # PERSONAL_TOKEN_EXPIRES_IN_MINUTES
    
Todo Tasks

- [x] Basic trait to issue tokens.
- [ ] Customize passport client to authorize the user.
- [ ] Add function to the trait to create a user.
- [ ] Make default routes for sessions management.
