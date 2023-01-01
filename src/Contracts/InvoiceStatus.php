<?php

namespace dnj\Invoice\Contracts;

enum InvoiceStatus: int
{
    case PAID = 1;
    case UNPAID = 2;
}
