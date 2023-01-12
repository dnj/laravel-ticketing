<?php

namespace dnj\Ticket\Database\Factories;

use dnj\Ticket\FileHelpers;
use dnj\Ticket\Models\TicketAttachment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

/**
 * @extends Factory<TicketAttachment>
 */
class TicketAttachmentFactory extends Factory
{
    use FileHelpers;

    protected $model = TicketAttachment::class;

    public function definition()
    {
        return [
            'name' => 'avatar.jpg',
            'mime' => 'image/jpeg',
            'size' => 63102,
            'file' => $this->createFile(),
        ];
    }

    public function createFile()
    {
        $file = config('ticket.attachment_root')->file('new');

        return $this->saveFile(UploadedFile::fake()->image('avatar.jpg'), '.jpg', $file);
    }
}
