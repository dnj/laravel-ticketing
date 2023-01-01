<?php

namespace dnj\Ticket\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use dnj\Ticket\Models\Department;

class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition()
    {
        return [
            'title' => fake()->sentence(2)
        ];
    }
}
