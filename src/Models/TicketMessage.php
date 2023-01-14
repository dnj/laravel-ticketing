<?php

namespace dnj\Ticket\Models;

use dnj\Ticket\Database\Factories\TicketMessageFactory;
use dnj\Ticket\ModelHelpers;
use dnj\UserLogger\Concerns\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketMessage extends Model
{
    use HasFactory;
    use ModelHelpers;
    use Loggable;

    protected static function newFactory()
    {
        return TicketMessageFactory::new();
    }

    protected $fillable = ['user_id', 'ticket_id', 'message', 'seen_at'];

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
}
