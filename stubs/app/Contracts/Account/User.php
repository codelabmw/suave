<?php

namespace App\Contracts\Account;

use App\Traits\MustVerifyEmail;
use App\Traits\CanSendTemporaryPassword;
use App\Contracts\Account\CanSendTemporaryPassword as CanSendTemporaryPasswordContract;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Auth\Authenticatable;

class User extends Model implements
    AuthenticatableContract,
    AuthorizableContract,
    CanSendTemporaryPasswordContract
{
    use Authenticatable, Authorizable, MustVerifyEmail, CanSendTemporaryPassword;
}