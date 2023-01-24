<?php

namespace dnj\Ticket\Contracts;

interface IDepartment
{
    public function getID(): int;

    public function getTitle(): string;

    public function getCreatedAt(): ?\DateTimeInterface;

    public function getUpdatedAt(): ?\DateTimeInterface;
}
