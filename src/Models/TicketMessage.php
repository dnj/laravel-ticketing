<?php

namespace dnj\Ticket\Models;

use dnj\Ticket\Contracts\IMessage;
use dnj\Ticket\Database\Factories\TicketMessageFactory;
use dnj\Ticket\ModelHelpers;
use dnj\UserLogger\Concerns\Loggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketMessage extends Model implements IMessage
{
    use HasFactory;
    use ModelHelpers;
    use Loggable;

    protected static function newFactory()
    {
        return TicketMessageFactory::new();
    }

    protected $fillable = ['user_id', 'ticket_id', 'message', 'seen_at'];
    protected $table = 'tickets_messages';

    public function user()
    {
        $model = $this->getUserModel();
        if (null === $model) {
            throw new \Exception('No user model is configured under ticket.user_model config');
        }

        return $this->belongsTo($model);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class, 'message_id');
    }

    public function getID(): int
    {
        return $this->id;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getTicketId(): int
    {
        return $this->ticket_id;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updated_at;
    }

    public function getSeenAt(): \DateTimeInterface
    {
        return $this->seen_at;
    }

    /**
     * @param array{user_id?:int,created_start_date?:DateTimeInterface,created_end_date?:DateTimeInterface,updated_start_date?:DateTimeInterface,updated_end_date?:DateTimeInterface}|null $filters
     */
    public function scopeFilter(Builder $q, ?array $filters): Builder
    {
        if (isset($filters['user_id'])) {
            $q->where('user_id', $filters['user_id']);
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
