<?php

use dnj\Ticket\Http\Controllers\DepartmentController;
use dnj\Ticket\Http\Controllers\TicketAttachmentController;
use dnj\Ticket\Http\Controllers\TicketController;
use dnj\Ticket\Http\Controllers\TicketMessageController;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;

Route::apiResource('departments', DepartmentController::class)->middleware('auth');

Route::middleware([SubstituteBindings::class, 'auth'])->group(function () {
    Route::apiResources([
        'tickets' => TicketController::class,
        'tickets.messages' => TicketMessageController::class,
        'ticketAttachments' => TicketAttachmentController::class,
    ]);
});
