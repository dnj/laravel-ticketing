<?php

namespace dnj\Ticket\Exceptions;

use dnj\Ticket\Contracts\Exceptions\IAttachmentAlreadyAsignedException;

class AttachmentAlreadyAsignedException extends \Exception implements IAttachmentAlreadyAsignedException
{
    public function __construct(
        protected int $attachmentId,
        protected int $messageId,
        string $message = 'This Attachment Already Assigned to A Message',
        int $code = 0,
        \Throwable|null $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getAttachmentId(): int
    {
        return $this->attachmentId;
    }

    public function getMessageId(): int
    {
        return $this->messageId;
    }
}
