<?php

use dnj\Ticket\Models\Department;

$factory->define(Department::class, function (Faker\Generator $faker) {
    return [
        'title' => $faker->sentence(2)
    ];
});
