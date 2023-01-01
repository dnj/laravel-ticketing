<?php

namespace dnj\Ticket\Models;

use Illuminate\Foundation\Auth\User as BaseUser;

class User extends BaseUser
{
    protected $table = 'users';
}
