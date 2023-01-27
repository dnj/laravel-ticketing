<?php

namespace dnj\Ticket\Models;

use dnj\Ticket\Contracts\ITicket;
use dnj\Ticket\Database\Factories\TicketFactory;
use dnj\Ticket\Enums\TicketStatus;
use dnj\Ticket\Exceptions\TicketTitleHasBeenDisabledException;
use dnj\Ticket\ModelHelpers;
use dnj\UserLogger\Concerns\Loggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model implements ITicket
{
    use HasFactory;
    use ModelHelpers;
    use Loggable;

    public static function hasTitle(): bool
    {
        return self::isTitleRequire();
    }

    protected static function newFactory()
    {
        return TicketFactory::new();
    }

    protected $fillable = ['title', 'client_id', 'department_id', 'status'];
    protected $casts = [
        'status' => TicketStatus::class,
    ];

    protected $with = ['client', 'department'];

    public function client()
    {
        $model = $this->getUserModel();
        if (null === $model) {
            throw new \Exception('No user model is configured under ticket.user_model config');
        }

        return $this->belongsTo($model);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function messages()
    {
        return $this->hasMany(TicketMessage::class);
    }

    public function getID(): int
    {
        return $this->id;
    }

    public function getClientID(): int
    {
        return $this->client_id;
    }

    public function getDepartmentID(): int
    {
        return $this->department_id;
    }

    public function getTitle(): string
    {
        if (!$this->isTitleRequire()) {
            throw new TicketTitleHasBeenDisabledException('Ticket title has disabled. check ticket.title config');
        }

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
     * @param array{title?:string,client_id?:int,department_id?:int,status?:TicketStatus[],created_start_date?:DateTimeInterface,created_end_date?:DateTimeInterface,updated_start_date?:DateTimeInterface,updated_end_date?:DateTimeInterface}|null $filters
     */
    public function scopeFilter(Builder $q, ?array $filters): Builder
    {
        if (isset($filters['title'])) {
            $q->where('title', 'like', '%'.$filters['title'].'%');
        }
        if (isset($filters['client_id'])) {
            $q->where('client_id', $filters['client_id']);
        }
        if (isset($filters['department_id'])) {
            $q->where('department_id', $filters['department_id']);
        }
        if (isset($filters['status'])) {
            $q->whereIn('status', $filters['status']);
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
