<?php

namespace dnj\Ticket\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketAttachment extends Model
{
    use HasFactory;

    protected $fillable = ['message_id', 'name', 'file', 'mime', 'size'];
    // protected $hidden = ['file'];
}
