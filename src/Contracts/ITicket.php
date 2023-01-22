<?php
namespace dnj\Ticket\Contracts;

use DateTimeInterface;

interface ITicket {
	public static function hasTitle(): bool;
	public function getID(): int;

	/**
	 * @throws Exceptions\ITicketTitleHasBeenDisabledException if called this method but titles was disabled
	 */
	public function getTitle(): string;
	
	public function getCreatedAt(): ?DateTimeInterface;
	public function getUpdatedAt(): ?DateTimeInterface;
}