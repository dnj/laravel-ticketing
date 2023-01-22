<?php

namespace dnj\Ticket\Contracts;

use DateTimeInterface;

interface IMessage
{
	public function getId(): int;
	public function getMessage(): string;
	public function getUserId(): int;
	public function getTicketId(): int;
	public function getCreatedAt(): ?DateTimeInterface;
	public function getUpdatedAt(): ?DateTimeInterface;
	public function getSeenAt(): ?DateTimeInterface;
}
