<?php

namespace dnj\Invoice\Contracts;

interface IPaymentMethod
{
    public function onApprove(): void;

    public function onReject(): void;
}
