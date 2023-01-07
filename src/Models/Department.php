<?php

namespace dnj\Ticket\Models;

use dnj\Ticket\Database\Factories\DepartmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return DepartmentFactory::new();
    }

    protected $fillable = ['title'];
}
