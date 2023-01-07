<?php

namespace dnj\Ticket\Database\Factories;

use dnj\Ticket\ModelHelpers;
use dnj\Ticket\Models\TicketMessage;
use dnj\Ticket\Models\Ticket;
use dnj\Ticket\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TicketMessage>
 */
class TicketMessageFactory extends Factory
{
    use ModelHelpers;

    protected $model = TicketMessage::class;

    public function definition()
    {
        $userModel = $this->getUserModel() ?? User::class;
        return [
            'message' => fake()->text(300),
            'user_id' => $userModel::factory(),
            'ticket_id' => Ticket::factory(),
        ];
    }

    public function withTicket(Ticket $ticket)
    {
        return $this->state(fn () => [
            'ticket_id' => $ticket,
        ]);
    }
}
