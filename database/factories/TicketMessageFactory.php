<?php

use dnj\Ticket\Models\TicketMessage;
use dnj\Ticket\Models\Ticket;
use dnj\Ticket\Models\User;

$factory->define(TicketMessage::class, function (Faker\Generator $faker) {
    return [
        'message' => $faker->text(300),
        'user_id' => User::all()->random()->first()->id,
        'ticket_id' => Ticket::all()->random()->first()->id
    ];
});
