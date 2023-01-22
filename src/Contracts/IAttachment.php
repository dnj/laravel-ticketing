<?php

namespace dnj\Ticket\Contracts;

use DateTimeInterface;
use dnj\Filesystem\Contracts\IFile;

interface IAttachment
{
	public function getID(): int;
	public function getMessageId(): ?int;
	public function getName(): string;
	public function getFile(): IFile;
	public function getMime(): string;
	public function getSize(): int;
	public function getCreatedAt(): ?DateTimeInterface;
	public function getUpdatedAt(): ?DateTimeInterface;
}
