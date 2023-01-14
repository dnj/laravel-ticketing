<?php

namespace dnj\Ticket\Tests\Feature;

use dnj\Ticket\Models\TicketAttachment;
use dnj\Ticket\Tests\Models\User;
use dnj\Ticket\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\Fluent\AssertableJson;

class TicketAttachmentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testStore(): void
    {
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
                $attachment = TicketAttachment::findOrFail($data['data'][0]['id']);
                $this->assertTrue($attachment->file->exists());
            });
    }

    public function testShow(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $attachments = TicketAttachment::factory()->create();

        $this->getJson(route('ticketAttachments.show', ['ticketAttachment' => $attachments->id]))
            ->assertStatus(200)
            ->assertDownload();
    }

    public function testDelete(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $attachments = TicketAttachment::factory()->create();
        $attachment = TicketAttachment::findOrFail($attachments->id);

        $this->deleteJson(route('ticketAttachments.destroy', ['ticketAttachment' => $attachments->id]))
            ->assertStatus(204);

        $this->assertFalse($attachment->file->exists());

        // Check the file don't delete if another record exist with same file path.
        $attachments = TicketAttachment::factory(2)->create();
        $attachment = TicketAttachment::findOrFail($attachments->first()->id);

        $this->deleteJson(route('ticketAttachments.destroy', ['ticketAttachment' => $attachments->first()->id]))
            ->assertStatus(204);

        $this->assertTrue($attachment->file->exists());
    }
}
