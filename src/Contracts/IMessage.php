<?php

namespace dnj\Ticket\Contracts;

interface IMessage
{
    public function getID(): int;

    public function getMessage(): string;

    public function getUserId(): int;

    public function getTicketId(): int;

    public function getCreatedAt(): ?\DateTimeInterface;

    public function getUpdatedAt(): ?\DateTimeInterface;

    public function getSeenAt(): ?\DateTimeInterface;
}
