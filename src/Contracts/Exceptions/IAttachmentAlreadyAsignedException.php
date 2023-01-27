<?php

namespace dnj\Ticket\Contracts\Exceptions;

interface IAttachmentAlreadyAsignedException extends \Throwable
{
    public function getAttachmentId(): int;

    public function getMessageId(): int;
}
