<?php

namespace dnj\Ticket\Models;

use dnj\Ticket\ModelHelpers;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketMessage extends Model
{
    use HasFactory, ModelHelpers;

    protected $fillable = ['user_id', 'ticket_id', 'message', 'seen_at'];

    // protected $touches = ['ticket'];

    protected static function booting(): void
    {
        static::creating(function ($ticketMessage) {
            $ticketMessage->user_id = auth()->user()->id;
        });
    }


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
        $this->belongsTo(Ticket::class);
    }
}
