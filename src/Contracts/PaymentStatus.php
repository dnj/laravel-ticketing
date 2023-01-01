<?php

namespace dnj\Invoice\Contracts;

enum PaymentStatus: int
{
    case APPROVED = 1;
    case PENDING = 2;
    case REJECTED = 3;
}
