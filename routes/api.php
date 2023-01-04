<?php

use dnj\Ticket\Http\Controllers\DepartmentController;
use dnj\Ticket\Http\Controllers\TicketController;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;

Route::middleware([SubstituteBindings::class])->group(
    function () {

        Route::middleware(['auth'])->group((function () {
            Route::resource('departments', DepartmentController::class);
            Route::resource('tickets', TicketController::class);
        }));
    }
);
