<?php

namespace App\Contracts\Account;

use App\Contracts\Account\CanSendTemporaryPassword as CanSendTemporaryPasswordContract;
use App\Traits\CanSendTemporaryPassword;
use App\Traits\MustVerifyEmail;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;

class User extends Model implements AuthenticatableContract, AuthorizableContract, CanSendTemporaryPasswordContract
{
    use Authenticatable, Authorizable, CanSendTemporaryPassword, MustVerifyEmail;
}
