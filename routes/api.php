<?php

use dnj\Ticket\Http\Controllers\DepartmentController;
use dnj\Ticket\Http\Controllers\TicketAttachmentController;
use dnj\Ticket\Http\Controllers\TicketController;
use dnj\Ticket\Http\Controllers\TicketMessageController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::apiResources([
        'departments' => DepartmentController::class,
        'tickets' => TicketController::class,
        'tickets.messages' => TicketMessageController::class,
        'ticketAttachments' => TicketAttachmentController::class,
    ]);
});
