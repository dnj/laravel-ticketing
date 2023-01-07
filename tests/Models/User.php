<?php

namespace dnj\Ticket\Tests\Models;

use dnj\Ticket\Tests\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as BaseUser;

class User extends BaseUser
{
    protected static function newFactory()
    {
        return UserFactory::new();
    }

    use HasFactory;

    protected $table = 'users';
}
