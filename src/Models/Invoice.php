<?php

namespace dnj\Invoice\Models;

use dnj\Currency\Contracts\IExchangeManager;
use dnj\Currency\Models\Currency;
use dnj\Invoice\Contracts\IInvoice;
use dnj\Invoice\Contracts\InvoiceStatus;
use dnj\Invoice\Contracts\PaymentStatus;
use dnj\Number\Contracts\INumber;
use dnj\Number\Laravel\Casts\Number as NumberCast;
use dnj\Number\Number;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model implements IInvoice
{
    protected $casts = [
        'amount' => NumberCast::class,
        'status' => InvoiceStatus::class,
        'meta' => 'array',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function user()
    {
        $model = $this->getUserModel();
        if (null === $model) {
            throw new Exception('No user model is configured under account.user_model config');
        }

        return $this->belongsTo($model);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function getID(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getUser(): ?Authenticatable
    {
        return $this->user;
    }

    public function getCurrencyId(): int
    {
        return $this->currency_id;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getAmount(): INumber
    {
        return $this->amount;
    }

    public function getPaidAmount(bool $includePendingPayments = false): INumber
    {
        /**
         * @var IExchangeManager
         */
        $exchange = app(IExchangeManager::class);
        $result = Number::fromInt(0);
        foreach ($this->getPayments() as $payment) {
            if (PaymentStatus::APPROVED === $payment->getStatus() or ($includePendingPayments and PaymentStatus::PENDING === $payment->getStatus())) {
                $amountInSameCurrency = $exchange->convert($payment->getAmount(), $this->getCurrencyId(), $payment->getCurrencyId(), true);
                $result = $result->add($amountInSameCurrency);
            }
        }

        return $result;
    }

    public function getUnpaidAmount(bool $includePendingPayments = true): INumber
    {
        $total = $this->getAmount();
        $paid = $this->getPaidAmount($includePendingPayments);

        return $total->sub($paid);
    }

    public function getStatus(): InvoiceStatus
    {
        return $this->status;
    }

    public function getCreateTime(): int
    {
        return $this->created_at->getTimestamp();
    }

    public function getUpdateTime(): int
    {
        return $this->modified_at?->getTimestamp() ?? $this->getCreateTime();
    }

    public function getPaidTime(): ?int
    {
        return $this->paid_at?->getTimestamp();
    }

    /**
     * @return Collection<Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    /**
     * @return Collection<Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function getMeta(): ?array
    {
        return $this->meta;
    }

    public function recalculateTotalAmount(): void
    {
        /**
         * @var IExchangeManager
         */
        $exchange = app(IExchangeManager::class);

        $result = Number::fromInput(0);
        foreach ($this->getProducts() as $product) {
            $amountInSameCurrency = $exchange->convert($product->getTotalAmount(), $this->getCurrencyId(), $product->getCurrencyId(), true);
            $result = $result->add($amountInSameCurrency);
        }
        $this->amount = $result;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}
