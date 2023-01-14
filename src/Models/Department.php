<?php

namespace dnj\Ticket\Models;

use dnj\Ticket\Database\Factories\DepartmentFactory;
use dnj\UserLogger\Concerns\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;
    use Loggable;

    protected static function newFactory()
    {
        return DepartmentFactory::new();
    }

    protected $fillable = ['title'];
}
