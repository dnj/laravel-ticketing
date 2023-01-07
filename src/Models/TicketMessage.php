<?php

namespace dnj\Ticket\Models;

use dnj\Ticket\Database\Factories\TicketMessageFactory;
use dnj\Ticket\ModelHelpers;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketMessage extends Model
{
    protected static function newFactory()
    {
        return TicketMessageFactory::new();
    }

    use HasFactory, ModelHelpers;

    protected $fillable = ['user_id', 'ticket_id', 'message', 'seen_at'];

    public function user()
    {
        $model = $this->getUserModel();
        if (null === $model) {
            throw new Exception('No user model is configured under ticket.user_model config');
        }

        return $this->belongsTo($model);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
