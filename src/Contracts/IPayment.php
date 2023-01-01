<?php

namespace dnj\Invoice\Contracts;

use dnj\Number\Contracts\INumber;

interface IPayment
{
    public function getID(): int;

    public function getInvoiceID(): int;

    public function getTransactionId(): ?int;

    public function getMethod(): string;

    public function getAmount(): INumber;

    public function getCreateTime(): int;

    public function getUpdateTime(): int;

    public function getStatus(): PaymentStatus;

    public function getMeta(): ?array;
}
