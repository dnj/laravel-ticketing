<?php

namespace dnj\Ticket;

trait ModelHelpers
{
    protected function isTitleRequire(): bool
    {
        return config('ticket.title');
    }

    protected function getUserModel(): ?string
    {
        return config('ticket.user_model');
    }

    protected function getUserTable(): ?string
    {
        $userModel = $this->getUserModel();

        $userTable = null;
        if ($userModel) {
            $userTable = (new $userModel())->getTable();
        }

        return $userTable;
    }
}
