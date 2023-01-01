<?php

use dnj\Ticket\Http\Controllers\DepartmentController;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;

Route::middleware([SubstituteBindings::class])->group(
    function () {
        Route::resource('departments', DepartmentController::class);
    }
);
