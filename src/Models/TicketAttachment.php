<?php

namespace dnj\Ticket\Models;

use dnj\Ticket\Casts\File;
use dnj\Ticket\Database\Factories\TicketAttachmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketAttachment extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return TicketAttachmentFactory::new();
    }

    protected $fillable = ['message_id', 'name', 'file', 'mime', 'size'];
    protected $hidden = ['file'];

    protected $casts = [
        'file' => File::class,
    ];
}
