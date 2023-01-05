<?php

namespace dnj\Ticket\Test\Fature;

use dnj\Ticket\Models\Department;
use dnj\Ticket\Models\Ticket;
use dnj\Ticket\Models\TicketMessage;
use dnj\Ticket\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use dnj\Ticket\Tests\TestCase;

class TicketMessageControllerTest extends TestCase
{
    use RefreshDatabase;


    public function testShowList(): void
    {
        $user = factory(User::class, 10)->create();
        factory(Department::class, 10)->create();
        $ticket = factory(Ticket::class, 10)->create()->first();

        $this->getJson(route('ticket.message.index', ['ticket' => $ticket->id]))
            ->assertStatus(401);

        $this->actingAs($user);

        factory(TicketMessage::class, 30)->create();
        $this->getJson(route('ticket.message.index', ['ticket' => $ticket->id]))
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json->has('data', 10);
                $json->etc();
            });
    }

    public function testStore(): void
    {
        $user = factory(User::class)->create();
        factory(Department::class, 10)->create();
        $ticket = factory(Ticket::class, 10)->create()->first();

        $this->postJson(route('ticket.message.store', ['ticket' => $ticket->id]))
            ->assertStatus(401);

        $this->actingAs($user);

        $ticketData = array(
            'message' => 'This is my test message for ticket.'
        );

        $this->postJson(route('ticket.message.store', ['ticket' => $ticket->id]))
            ->assertStatus(422)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(["message", "errors", "errors.message"]);
            });

        $this->postJson(route('ticket.message.store', ['ticket' => $ticket->id]), $ticketData)
            ->assertStatus(201)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(["data", "data.id", "data.message", "data.user"]);
                $json->etc();
            });
    }


    public function testUpdate(): void
    {

        $this->putJson(route('ticket.message.update', ['ticket' => 3, 'message' => 2]))
            ->assertStatus(401);

        $user = factory(User::class, 10)->create();
        factory(Department::class, 10)->create();
        factory(Ticket::class, 10)->create();
        $this->actingAs($user);

        factory(TicketMessage::class, 30)->create();
        $messageData = array(
            'message' => 'Update ticket message'
        );

        $this->putJson(route('ticket.message.update', ['ticket' => 50, 'message' => 100]), $messageData)
            ->assertStatus(404);


        $this->putJson(route('ticket.message.update', ['ticket' => 1, 'message' => 2]))
            ->assertStatus(422)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(["message", "errors", "errors.message"]);
            });


        $this->putJson(route('ticket.message.update', ['ticket' => 1, 'message' => 2]), $messageData)
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) use ($messageData) {
                $json->hasAll(["data", "data.id", "data.user"]);
                $json->where('data.message', $messageData["message"]);
                $json->etc();
            });
    }

    public function testDestroy(): void
    {

        $this->deleteJson(route('ticket.message.destroy', ['ticket' => 2, 'message' => 1]))
            ->assertStatus(401);

        $user = factory(User::class, 10)->create();
        $this->actingAs($user);
        factory(Department::class, 10)->create();
        factory(Ticket::class, 10)->create()->first();
        factory(TicketMessage::class, 30)->create();

        $this->deleteJson(route('ticket.message.destroy', ['ticket' => 20, 'message' => 50]))
            ->assertStatus(404);


        $this->deleteJson(route('ticket.message.destroy', ['ticket' => 2, 'message' => 1]))
            ->assertStatus(204);
    }
}
