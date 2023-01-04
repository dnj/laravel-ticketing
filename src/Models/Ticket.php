<?php

namespace dnj\Ticket\Models;

use dnj\Ticket\Enums\TicketStatus;
use dnj\Ticket\ModelHelpers;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory, ModelHelpers;

    protected $fillable = ['title', 'client_id', 'department_id', 'status'];
    protected $casts = [
        'status' => TicketStatus::class,
    ];

    protected $with = ['user', 'department'];

    protected static function booting(): void
    {
        static::creating(function ($ticket) {
            if (!isset($ticket->client_id))
                $ticket->client_id = auth()->user()->id;
        });
    }

    public function user()
    {
        $model = $this->getUserModel();
        if (null === $model) {
            throw new Exception('No user model is configured under ticket.user_model config');
        }

        return $this->belongsTo($model, 'client_id');
    }


    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function messages()
    {
        return $this->hasMany(TicketMessage::class);
    }


    public function scopeFilter($q, $request)
    {
        return $q->when($request->input('title'), function ($q, $title) {
            return $q->where('title', 'like', '%' . $title . '%');
        })
            ->when($request->input('client_id'), function ($q, $client) {
                return $q->where('client_id', $client);
            })
            ->when($request->input('status'), function ($q, $status) {
                return $q->whereIn('status', $status);
            })
            ->when($request->input('created_start_date'), function ($q, $created_start_date) use ($request) {
                $created_end_date = $request->input('created_end_date', now());
                return $q->whereBetween('created_at', [$created_start_date, $created_end_date]);
            })
            ->when($request->input('updated_start_date'), function ($q, $updated_start_date) use ($request) {
                $updated_end_date = $request->input('updated_end_date', now());
                return $q->whereBetween('updated_at', [$updated_start_date, $updated_end_date]);
            });
    }
}
