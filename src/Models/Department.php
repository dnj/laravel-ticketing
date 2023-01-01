<?php

namespace dnj\Ticket\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{

    protected static function newFactory()
    {
        return \dnj\Ticket\Database\Factories\DepartmentFactory::new();
    }

    use HasFactory;
    protected $fillable = ['title'];
}
