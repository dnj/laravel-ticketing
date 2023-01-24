<?php

namespace dnj\Ticket\Managers\Concerns;

use Illuminate\Database\Eloquent\Model;

trait WorksWithLog
{
    protected function saveLog(?Model $model, ?array $changes, string $log): void
    {
        if ($this->getSaveLogs()) {
            $this->userLogger
                ->withRequest(request())
                ->performedOn($model)
                ->withProperties($changes)
                ->log($log);
        }
    }
}
