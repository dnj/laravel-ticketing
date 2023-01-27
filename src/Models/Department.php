<?php

namespace dnj\Ticket\Models;

use dnj\Ticket\Contracts\IDepartment;
use dnj\Ticket\Database\Factories\DepartmentFactory;
use dnj\UserLogger\Concerns\Loggable;
use Illuminate\Database\Eloquent\Builder;
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

    /**
     * @param array{title?:string,created_start_date?:string,created_end_date?:string,updated_start_date?:string,updated_end_date?:string}|null $filters
     */
    public function scopeFilter(Builder $q, ?array $filters): Builder
    {
        if (isset($filters['title'])) {
            $q->where('title', 'like', '%'.$filters['title'].'%');
        }
        if (isset($filters['created_start_date'])) {
            $q->where('created_at', '>=', $filters['created_start_date']);
        }
        if (isset($filters['created_end_date'])) {
            $q->where('created_at', '<', $filters['created_end_date']);
        }
        if (isset($filters['updated_start_date'])) {
            $q->where('updated_at', '>=', $filters['updated_start_date']);
        }
        if (isset($filters['updated_end_date'])) {
            $q->where('updated_at', '>=', $filters['updated_end_date']);
        }

        return $q;
    }
}
