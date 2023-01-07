<?php

namespace dnj\Ticket\Models;

use dnj\Ticket\Database\Factories\TicketFactory;
use dnj\Ticket\Enums\TicketStatus;
use dnj\Ticket\ModelHelpers;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected static function newFactory()
    {
        return TicketFactory::new();
    }

    use HasFactory, ModelHelpers;

    protected $fillable = ['title', 'client_id', 'department_id', 'status'];
    protected $casts = [
        'status' => TicketStatus::class,
    ];

    protected $with = ['client', 'department'];

    public function client()
    {
        $model = $this->getUserModel();
        if (null === $model) {
            throw new Exception('No user model is configured under ticket.user_model config');
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
}
