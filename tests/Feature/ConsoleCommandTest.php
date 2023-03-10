<?php

namespace dnj\Ticket\Tests\Feature;

use dnj\Ticket\Models\TicketAttachment;
use dnj\Ticket\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ConsoleCommandTest extends TestCase
{
    use RefreshDatabase;

    public function testTicketAttachmentPurge(): void
    {
        TicketAttachment::factory(5)->create();
        $this->artisan('ticket:attachment:purge')->assertSuccessful();

        $files = TicketAttachment::factory(5)->create(['created_at' => now()->subMinutes(12)]);
        $this->artisan('ticket:attachment:purge')->assertSuccessful();
    }
}
