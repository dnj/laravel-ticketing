<?php

namespace dnj\Ticket\Database\Factories;

use dnj\Ticket\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Department>
 */
class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition()
    {
        return [
            'title' => fake()->sentence(2),
        ];
    }
}
