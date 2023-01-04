<?php

use dnj\Ticket\Models\Department;
use dnj\Ticket\Models\Ticket;
use dnj\Ticket\Models\User;

$factory->define(Ticket::class, function (Faker\Generator $faker) {
    return [
        'title' => $faker->sentence(3),
        'client_id' => User::all()->random()->first()->id,
        'department_id' => Department::all()->random()->first()->id
    ];
});
