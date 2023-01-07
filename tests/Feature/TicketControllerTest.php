<?php

namespace dnj\Ticket\Tests\Feature;

use dnj\Ticket\Enums\TicketStatus;
use dnj\Ticket\Models\Department;
use dnj\Ticket\Models\Ticket;
use dnj\Ticket\Tests\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use dnj\Ticket\Tests\TestCase;

class TicketControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testShowList(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
    
        $ticket = Ticket::factory(10)->create();
        $this->getJson(route('tickets.index'))
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

        $department = Department::factory()->create();
        $data = array(
            'title' =>  'Test Ticket',
            'department_id' => $department->id,
            'message' => 'This is my first message for package.'
        );

        $this->postJson(route('tickets.store'), $data)
            ->assertStatus(201)
            ->assertJson(function (AssertableJson $json) use ($data) {
                $json->hasAll(["data", "data.client", "data.department"]);
                $json->where('data.department_id', $data["department_id"]);
                $json->etc();
            });
    }

    public function testSearch(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Ticket::factory(5)->create();
        $client = User::factory()->create();
        $ticket = Ticket::factory()->withClientId($client->id)->create();

        $this->getJson(route('tickets.index', ['client_id' => $client->id]))
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) use ($client) {
                $json->has('data', 1);
                $json->whereContains('data.0.client_id', $client->id);
                $json->hasAll(["data.0.client", "data.0.department"]);
                $json->etc();
            });
    }

    public function testShow(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);


        $ticket = Ticket::factory()->create();

        $this->getJson(route('tickets.show', ['ticket' => $ticket->id]))
            ->assertStatus(200)
            ->assertJson(array(
                'data' => $ticket->toArray()
            ));
    }

    public function testUpdate(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $department = Department::factory()->create();
        $ticket = Ticket::factory()->create();

        $changes = array(
            'title' => 'Update ticket',
            'department_id' => $department->id,
            'status' => TicketStatus::IN_PROGRESS->value,
        );

        $this->putJson(route('tickets.update', ['ticket' => $ticket->id]), $changes)
            ->assertStatus(200)
            ->assertJson(array(
                'data' => array(
                    'id' => $ticket->id,
                    'title' => $changes['title'],
                    'department_id' => $changes['department_id'],
                    'status' => $changes['status'],
                )
            ));
    }

    public function testDestroy(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
    
        $ticket = Ticket::factory()->create();
        $this->deleteJson(route('tickets.destroy', ['ticket' => $ticket->id]))
            ->assertStatus(204);
    }
}
