<?php

namespace dnj\Ticket\Enums;

enum TicketStatus: string
{
    case UNREAD = 'UNREAD';
    case READ = 'READ';
    case IN_PROGRESS = 'IN_PROGRESS';
    case ANSWERED = 'ANSWERED';
    case CLOSED = 'CLOSED';

    /**
     * @return string[]
     */
    public static function getAllValues(): array
    {
        return array_column(TicketStatus::cases(), 'value');
    }
}
