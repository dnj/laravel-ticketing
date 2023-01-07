<?php

namespace dnj\Ticket\Tests\Models;

use dnj\Ticket\Tests\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as BaseUser;

class User extends BaseUser
{
    use HasFactory;

    protected static function newFactory()
    {
        return UserFactory::new();
    }

    protected $table = 'users';
}
