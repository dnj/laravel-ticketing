<?php

use dnj\Ticket\Models\Ticket;
use dnj\Ticket\Models\User;

$factory->define(Department::class, function (Faker\Generator $faker) {
    return [
        'message' => $faker->text(300),
        'user_id' => auth()->loginUsingId(User::all()->random()->first()->id),
        'ticket_id' => Ticket::all()->random()->first()->id
    ];
});
