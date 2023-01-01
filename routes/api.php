<?php

use dnj\Ticket\Http\Controllers\DepartmentController;
use Illuminate\Support\Facades\Route;

Route::resource('departments', DepartmentController::class);
