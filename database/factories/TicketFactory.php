<?php

namespace dnj\Ticket\Database\Factories;

use dnj\Ticket\ModelHelpers;
use dnj\Ticket\Models\Department;
use dnj\Ticket\Models\Ticket;
use dnj\Ticket\Tests\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    use ModelHelpers;

    protected $model = Ticket::class;

    public function definition()
    {
        $userModel = $this->getUserModel() ?? User::class;

        return [
            'title' => fake()->sentence(3),
            'client_id' => $userModel::factory(),
            'department_id' => Department::factory(),
        ];
    }

    public function withDepartment(Department $department)
    {
        return $this->state(fn () => [
            'department_id' => $department,
        ]);
    }

    public function withClientId(int $client)
    {
        return $this->state(fn () => [
            'client_id' => $client,
        ]);
    }
}
