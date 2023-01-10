<?php

namespace dnj\Ticket\Tests\Feature;

use dnj\Ticket\Tests\Models\User;
use dnj\Filesystem\Contracts\IFile;
use dnj\Ticket\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\Fluent\AssertableJson;

class TicketAttachmentControllerTest extends TestCase
{
    use RefreshDatabase;
    private IFile $file;

    public function testStore(): void
    {
        $this->file = app()->make(IFile::class);

        $user = User::factory()->create();
        $this->actingAs($user);

        $image = [
            'attachments' => [UploadedFile::fake()->image('avatar.jpg')],
        ];

        $this->postJson(route('ticketAttachments.store'), $image)
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['data', 'data.0.name', 'data.0.id', 'data.0.mime', 'data.0.size']);
                $data = $json->toArray();
                $this->file->unserialize($data['data'][0]['file']);
                $this->assertTrue($this->file->exists());
            });
    }


    public function testDelete(): void
    {
        $this->testStore();
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->deleteJson(route('ticketAttachments.destroy', ['ticketAttachment' => 1]))
            ->assertStatus(204);
        $this->assertFalse($this->file->exists());

        // Check the file don't delete if another record exist with same file path.
        $this->testStore();
        $this->testStore();

        $this->deleteJson(route('ticketAttachments.destroy', ['ticketAttachment' => 2]))
            ->assertStatus(204);
        $this->assertTrue($this->file->exists());
    }
}
