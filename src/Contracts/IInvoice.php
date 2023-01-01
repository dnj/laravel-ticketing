<?php

namespace dnj\Invoice\Contracts;

use dnj\Number\Contracts\INumber;

interface IInvoice
{
    public function getID(): int;

    public function getUserId(): int;

    public function getCurrencyId(): int;

    public function getTitle(): string;

    public function getAmount(): INumber;

    public function getPaidAmount(bool $includePendingPayments = false): INumber;

    public function getUnpaidAmount(bool $includePendingPayments = true): INumber;

    public function getStatus(): InvoiceStatus;

    public function getCreateTime(): int;

    public function getUpdateTime(): int;

    public function getPaidTime(): ?int;

    /**
     * @return iterable<IProduct>
     */
    public function getProducts(): iterable;

    /**
     * @return iterable<IPayment>
     */
    public function getPayments(): iterable;

    public function getMeta(): ?array;
}
