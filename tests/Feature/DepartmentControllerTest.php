<?php

namespace dnj\Ticket\Test\Fature;

use dnj\Ticket\Models\Department;
use dnj\Ticket\Models\User;
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

        $user = factory(User::class)->create();
        $this->actingAs($user);

        $this->getJson(route('departments.index'))
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json->has('data', 10);
                $json->etc();
            });
    }

    public function testStore(): void
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $departmentData = array(
            'title' =>  'Test Department'
        );

        $this->postJson(route('departments.store'))
            ->assertStatus(422)
            ->assertJson(function (AssertableJson $json) {
                $json->has("message");
                $json->has("errors");
                $json->hasAll(["errors.title"]);
            });

        $this->postJson(route('departments.store'), $departmentData)
            ->assertStatus(201)
            ->assertJson(function (AssertableJson $json) use ($departmentData) {
                $json->has("department");
                $json->hasAll(["department.id", "department.title"]);
                $json->where('department.title', $departmentData["title"]);
                $json->etc();
            });
    }

    public function testSearch(): void
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        Department::factory(10)->create();

        $this->testStore();

        $this->getJson(route('departments.index', ['title' => 'Test']))
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json->has('data', 1);
                $json->whereContains('data.0.title', 'Test Department');
                $json->etc();
            });
    }

    public function testShow(): void
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        Department::factory(10)->create();

        $departmentData = array(
            'id' =>  1
        );

        $this->getJson(route('departments.show', ['department' => 20]))
            ->assertStatus(404);

        $this->getJson(route('departments.show', ['department' => 1]))
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) use ($departmentData) {
                $json->has("department");
                $json->hasAll(["department.id", "department.title"]);
                $json->where('department.id', $departmentData["id"]);
                $json->etc();
            });
    }


    public function testUpdate(): void
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        Department::factory(10)->create();

        $departmentData = array(
            'title' =>  'Update Department'
        );

        $this->putJson(route('departments.update', ['department' => 20]), $departmentData)
            ->assertStatus(404);


        $this->putJson(route('departments.update', ['department' => 1]))
            ->assertStatus(422)
            ->assertJson(function (AssertableJson $json) {
                $json->has("message");
                $json->has("errors");
                $json->hasAll(["errors.title"]);
            });

        $this->putJson(route('departments.update', ['department' => 1]), $departmentData)
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) use ($departmentData) {
                $json->has("department");
                $json->hasAll(["department.id", "department.title"]);
                $json->where('department.title', $departmentData["title"]);
                $json->etc();
            });
    }


    public function testDelete(): void
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        Department::factory(10)->create();

        $this->deleteJson(route('departments.destroy', ['department' => 20]))
            ->assertStatus(404);


        $this->deleteJson(route('departments.destroy', ['department' => 3]))
            ->assertStatus(204);
    }
}
