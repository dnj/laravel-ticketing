<?php
namespace dnj\Ticket\Contracts;

use DateTimeInterface;

interface IDepartment {
	public function getID(): int;
	public function getTitle(): string;
	public function getCreatedAt(): ?DateTimeInterface;
	public function getUpdatedAt(): ?DateTimeInterface;
}