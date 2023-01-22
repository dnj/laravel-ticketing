<?php

namespace dnj\Ticket\Contracts;

interface ICanLog {
	public function setSaveLogs(bool $save): void;
    public function getSaveLogs(): bool;
}
