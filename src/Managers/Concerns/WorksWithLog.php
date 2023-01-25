<?php

namespace dnj\Ticket\Managers\Concerns;

trait WorksWithLog
{
    protected function saveLog(?array $changes, string $log): void
    {
        if ($this->getSaveLogs()) {
            $this->userLogger
                ->withRequest(request())
                ->performedOn($this->model)
                ->withProperties($changes)
                ->log($log);
        }
    }
}
