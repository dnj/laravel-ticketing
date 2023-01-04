<?php

namespace dnj\Ticket\Tests\Feature;

use dnj\Ticket\Models\Department;
use dnj\Ticket\Models\Ticket;
use dnj\Ticket\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use dnj\Ticket\Tests\TestCase;

class TicketControllerTest extends TestCase
{
    use RefreshDatabase;


    public function testShowList(): void
    {
        $user =  factory(user::class)->create();
        factory(Department::class, 10)->create();
        factory(Ticket::class, 10)->create();


        $this->getJson(route('tickets.index'))
            ->assertStatus(401);

        $this->actingAs($user);

        $this->getJson(route('tickets.index'))
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json->has('data', 10);
                $json->etc();
            });
    }

    public function testStore(): void
    {
        $user = factory(User::class, 10)->create();
        $department = factory(Department::class)->create();

        $this->postJson(route('tickets.store'))
            ->assertStatus(401);

        $this->actingAs($user[0]);

        $ticketData = array(
            'title' =>  'Test Ticket',
            'department_id' => $department->id,
            'message' => 'This is my first message for package.'
        );

        $this->postJson(route('tickets.store'))
            ->assertStatus(422)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(["message", "errors", "errors.department_id", "errors.message"]);
                $json->missing('client_id');
            });

        $ticketData["client_id"] = 200;
        $this->postJson(route('tickets.store'), $ticketData)
            ->assertStatus(422)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(["message", "errors", "errors.client_id"]);
            });

        $ticketData["client_id"] = 4;
        $this->postJson(route('tickets.store'), $ticketData)
            ->assertStatus(201)
            ->assertJson(function (AssertableJson $json) use ($ticketData) {
                $json->hasAll(["ticket", "messages", "ticket.user", "ticket.department"]);
                $json->where('ticket.department_id', $ticketData["department_id"]);
                $json->etc();
            });
    }

    public function testSearch(): void
    {
        $user =  factory(user::class)->create();
        factory(Department::class, 10)->create();
        factory(Ticket::class, 10)->create();

        $this->getJson(route('tickets.index', ['client_id' => 4]))
            ->assertStatus(401);

        $this->testStore();
        $this->actingAs($user);

        $this->getJson(route('tickets.index', ['client_id' => 4]))
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json->has('data', 1);
                $json->whereContains('data.0.client_id', 4);
                $json->hasAll(["data.0.user", "data.0.department"]);
                $json->etc();
            });
    }

    public function testShow(): void
    {
        $user =  factory(user::class)->create();
        factory(Department::class, 10)->create();
        factory(Ticket::class, 10)->create();

        $this->getJson(route('tickets.show', ['ticket' => 200]))
            ->assertStatus(401);

        $this->actingAs($user);

        $ticketData = array(
            'id' =>  1
        );

        $this->getJson(route('tickets.show', ['ticket' => 200]))
            ->assertStatus(404);

        $this->getJson(route('tickets.show', ['ticket' => 1]))
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) use ($ticketData) {
                $json->hasAll(["ticket", "messages", "ticket.user", "ticket.department"]);
                $json->where('ticket.id', $ticketData["id"]);
                $json->etc();
            });
    }

    public function testUpdate(): void
    {
        $user =  factory(user::class)->create();
        $department =  factory(Department::class)->create();
        factory(Ticket::class, 10)->create();

        $this->putJson(route('tickets.update', ['ticket' => 20]))
            ->assertStatus(401);

        $this->actingAs($user);

        $ticketData = array(
            'title' => 'Update ticket',
            'department_id' => $department->id
        );

        $this->putJson(route('tickets.update', ['ticket' => 20]), $ticketData)
            ->assertStatus(404);


        $this->putJson(route('tickets.update', ['ticket' => 1]))
            ->assertStatus(422)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(["message", "errors", "errors.department_id"]);
            });

        $this->putJson(route('tickets.update', ['ticket' => 1]), $ticketData)
            ->assertStatus(422)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(["message", "errors", "errors.status"]);
            });

        $ticketData["status"] = 'read';
        $this->putJson(route('tickets.update', ['ticket' => 1]), $ticketData)
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) use ($ticketData) {
                $json->hasAll(["ticket", "ticket.id", "ticket.title"]);
                $json->where('ticket.status', $ticketData["status"]);
                $json->etc();
            });
    }

    public function testDestroy(): void
    {
        $user =  factory(user::class)->create();
        factory(Department::class)->create();
        factory(Ticket::class, 10)->create();

        $this->deleteJson(route('tickets.destroy', ['ticket' => 20]))
            ->assertStatus(401);

        $this->actingAs($user);

        $this->deleteJson(route('tickets.destroy', ['ticket' => 20]))
            ->assertStatus(404);

        $this->deleteJson(route('tickets.destroy', ['ticket' => 1]))
            ->assertStatus(204);
    }

    public function testStoreTicketMessage(): void
    {

        $user =  factory(user::class)->create();
        factory(Department::class)->create();
        factory(Ticket::class, 10)->create();

        $this->postJson(route('tickets.send_message', ['ticket' => 200]))
            ->assertStatus(401);

        $this->actingAs($user);
        $ticketData = array(
            'message' => 'This is new message for package.'
        );

        $this->postJson(route('tickets.send_message', ['ticket' => 200]))
            ->assertStatus(404);

        $this->postJson(route('tickets.send_message', ['ticket' => 2]))
            ->assertStatus(422)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(["message", "errors", "errors.message"]);
                $json->missing('client_id');
            });

        $this->postJson(route('tickets.send_message', ['ticket' => 2]), $ticketData)
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) use ($ticketData) {
                $json->hasAll(["ticket", "messages", "ticket.user", "ticket.department"]);
                $json->where('ticket.id', 2);
                $json->etc();
            });
    }
}
