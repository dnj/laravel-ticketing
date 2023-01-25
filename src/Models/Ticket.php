<?php

namespace dnj\Ticket\Models;

use dnj\Ticket\Contracts\ITicket;
use dnj\Ticket\Database\Factories\TicketFactory;
use dnj\Ticket\Enums\TicketStatus;
use dnj\Ticket\Exceptions\TicketTitleHasBeenDisabledException;
use dnj\Ticket\ModelHelpers;
use dnj\UserLogger\Concerns\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model implements ITicket
{
    use HasFactory;
    use ModelHelpers;
    use Loggable;

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

    public static function hasTitle(): bool
    {
        return self::isTitleRequire();
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
}
