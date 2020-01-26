<?php


namespace Quadram\LaravelPassport\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Quadram\LaravelPassport\Traits\LaravelPassportTrait;

class User extends Model
{
    use LaravelPassportTrait, Authenticatable;

    protected $guarded = [];
}