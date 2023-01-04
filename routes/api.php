<?php

use dnj\Ticket\Http\Controllers\DepartmentController;
use dnj\Ticket\Http\Controllers\TicketController;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;

Route::middleware([SubstituteBindings::class])->group(
    function () {

        Route::middleware(['auth'])->group((function () {
            Route::resource('departments', DepartmentController::class)->middleware('auth');
            Route::resource('tickets', TicketController::class)->middleware('auth');
            Route::post('tickets/send-message/{ticket}', [TicketController::class, 'storeTicketMessage'])
                ->name('tickets.send_message')
                ->middleware('auth');
        }));
    }
);
