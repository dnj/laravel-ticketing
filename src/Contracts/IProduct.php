<?php

namespace dnj\Invoice\Contracts;

use dnj\Number\Contracts\INumber;

interface IProduct
{
    public function getID(): int;

    public function getInvoiceID(): int;

    public function getPrice(): INumber;

    public function getDiscount(): INumber;

    public function getCurrencyId(): int;

    public function getCount(): int;

    public function getTotalAmount(): INumber;

    /**
     * @return array<int,INumber>
     */
    public function getDistributionPlan(): array;

    public function getDistribution(): ?array;

    public function getCreateTime(): int;

    public function getUpdateTime(): int;

    public function getMeta(): ?array;

    public function getTitle(): string;

    public function getDescription(): string;
}
