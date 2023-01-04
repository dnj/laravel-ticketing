<?php

namespace dnj\Ticket\Enums;

enum TicketStatus: string
{
    case UNREAD = "unread";
    case READ = "read";
    case IN_PROGRESS = "in_progress";
    case ANSWERED = "answered";
    case CLOSED = "closed";

    public static function getAllValues(): array
    {
        return array_column(TicketStatus::cases(), 'value');
    }
}
