<?php

namespace dnj\Ticket\Managers\Concerns;

trait WorksWithLog
{
    protected function saveLog(string $log): void
    {
        if ($this->getSaveLogs()) {
            $changes = 'deleted' == $log ? $this->model->toArray() : $this->model->changesForLog();
            $this->userLogger
                ->withRequest(request())
                ->performedOn($this->model)
                ->withProperties($changes)
                ->log($log);
        }
    }
}
