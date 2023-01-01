<?php

namespace dnj\Invoice\Contracts;

use dnj\Number\Contracts\INumber;

/**
 * @phpstan-type ProductInput array{price:INumber,discount:INumber,currencyId:int,count:int,distributionPlan:array<int,INumber>,localizedDetails:array<string,array{title:string}>,meta?:array|null}
 * @phpstan-type UpdateProductInput array{id?:int,price?:INumber,discount?:INumber,currencyId?:int,count?:int,distributionPlan?:array<int,INumber>,localizedDetails?:array<string,array{title:string}>,meta?:array|null}
 */
interface IInvoiceManager
{
    /**
     * @param array<ProductInput>               $products
     * @param array<string,array{title:string}> $localizedDetails
     */
    public function create(int $userId, int $currencyId, array $products, array $localizedDetails, ?array $meta): IInvoice;

    public function delete(int $invoiceId): void;

    /**
     * @param array{userId?:int,currencyId?:int,meta?:array|null,products?:array<UpdateProductInput>,localizedDetails?:array<string,array{title:string}>} $changes
     */
    public function update(int $invoiceId, array $changes): IInvoice;

    /**
     * @param ProductInput $product
     */
    public function addProductToInvoice(int $invoiceId, array $product): IProduct;

    /**
     * @param array{price?:INumber,discount?:INumber,currencyId?:int,count?:int,distributionPlan?:array<int,INumber>,meta?:array|null} $changes
     */
    public function updateProduct(int $productId, array $changes): IProduct;

    public function deleteProduct(int $productId): IInvoice;

    /**
     * @param int[]                             $invoiceIds
     * @param array<string,array{title:string}> $localizedDetails
     */
    public function merge(array $invoiceIds, array $localizedDetails): IInvoice;

    /**
     * @param int[][]
     *
     * @return IInvoice[]
     */
    public function split(array $invoiceIds): array;

    public function addPaymentToInvoice(int $invoiceId, string $type, INumber $amount, PaymentStatus $status, ?array $meta): IPayment;

    public function approvePayment(int $paymentId): IPayment;

    public function rejectPayment(int $paymentId): IPayment;
}
