<?php

namespace dnj\Ticket\Test\Fature;

use dnj\Ticket\Models\Ticket;
use dnj\Ticket\Models\TicketMessage;
use dnj\Ticket\Tests\Models\User;
use dnj\Ticket\Tests\TestCase;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;

class TicketMessageControllerTest extends TestCase
{
    use RefreshDatabase;


    public function testShowList(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $ticket = Ticket::factory()->create();
        TicketMessage::factory(10)->withTicket($ticket)->create();

        $this->getJson(route('tickets.messages.index', ['ticket' => $ticket->id]))
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json->has('data', 10);
                $json->etc();
            });
    }

    public function testStore(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $ticket = Ticket::factory()->create();

        $data = [
            'message' => 'This is my test message for ticket.',
        ];

        $params = [
            'ticket' => $ticket->id,
        ];

        $this->postJson(route('tickets.messages.store', $params))
            ->assertStatus(422)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(["message", "errors", "errors.message"]);
            });

        $this->postJson(route('tickets.messages.store', $params), $data)
            ->assertStatus(201)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(["data", "data.id", "data.message", "data.user"]);
                $json->etc();
            });
    }


    public function testUpdate(): void
    {

        $user = User::factory()->create();
        $this->actingAs($user);

        $ticket = Ticket::factory()->create();
        $ticketMessage =  TicketMessage::factory()->withTicket($ticket)->create();

        $data = [
            'message' => 'Update ticket message'
        ];

        $params = [
            'ticket' => $ticket->id,
            'message' => $ticketMessage->id,
        ];

        $this->putJson(route('tickets.messages.update', $params))
            ->assertStatus(422)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(["message", "errors", "errors.message"]);
            });

        $this->putJson(route('tickets.messages.update', $params), $data)
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) use ($data) {
                $json->hasAll(["data", "data.id", "data.user"]);
                $json->where('data.message', $data["message"]);
                $json->etc();
            });
    }

    public function testDestroy(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $ticket = Ticket::factory()->create();
        $ticketMessage =  TicketMessage::factory()->withTicket($ticket)->create();

        $params = [
            'ticket' => $ticket->id,
            'message' => $ticketMessage->id,
        ];

        $this->deleteJson(route('tickets.messages.destroy', $params))
            ->assertStatus(204);
    }
}
