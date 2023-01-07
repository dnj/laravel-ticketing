<?php

namespace dnj\Ticket\Tests\Feature;

use dnj\Ticket\Models\Department;
use dnj\Ticket\Tests\Models\User;
use dnj\Ticket\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;

class DepartmentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testShowList(): void
    {
        Department::factory(10)->create();

        $this->getJson(route('departments.index'))
            ->assertStatus(401);

        $user = User::factory()->create();
        $this->actingAs($user);

        $this->getJson(route('departments.index'))
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json->has('data', 10);
                $json->hasAll(['path', 'next_page_url', 'prev_page_url']);
                $json->etc();
            });
    }

    public function testStore(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $data = [
            'title' => 'Test Department',
        ];

        $this->postJson(route('departments.store'), $data)
            ->assertStatus(201)
            ->assertJson(function (AssertableJson $json) use ($data) {
                $json->has('data');
                $json->hasAll(['data.id', 'data.title']);
                $json->where('data.title', $data['title']);
                $json->etc();
            });
    }

    public function testShow(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $department = Department::factory()->create();

        $this->getJson(route('departments.show', ['department' => $department->id]))
            ->assertStatus(200)
            ->assertJson([
                'data' => $department->toArray(),
            ]);
    }

    public function testUpdate(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $department = Department::factory()->create();
        $changes = [
            'title' => 'Update Department',
        ];

        $this->putJson(route('departments.update', ['department' => 1]), $changes)
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $department->id,
                    'title' => $changes['title'],
                ],
            ]);
    }

    public function testDelete(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $department = Department::factory()->create();

        $this->deleteJson(route('departments.destroy', [
            'department' => $department->id,
        ]))
            ->assertStatus(204);
    }
}
