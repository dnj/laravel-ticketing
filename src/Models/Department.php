<?php

namespace dnj\Ticket\Models;

use dnj\Ticket\Contracts\IDepartment;
use dnj\Ticket\Database\Factories\DepartmentFactory;
use dnj\UserLogger\Concerns\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model implements IDepartment
{
    use HasFactory;
    use Loggable;

    protected static function newFactory()
    {
        return DepartmentFactory::new();
    }

    protected $fillable = ['title'];

    public function getID(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updated_at;
    }
}
