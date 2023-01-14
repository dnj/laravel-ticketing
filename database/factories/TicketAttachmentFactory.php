<?php

namespace dnj\Ticket\Database\Factories;

use dnj\Ticket\Models\TicketAttachment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

/**
 * @extends Factory<TicketAttachment>
 */
class TicketAttachmentFactory extends Factory
{
    protected $model = TicketAttachment::class;

    public function definition()
    {
        $file = UploadedFile::fake()->image('avatar.jpg');
        $file = TicketAttachment::putFileInStorage($file);

        return [
            'name' => 'avatar.jpg',
            'mime' => 'image/jpeg',
            'size' => 63102,
            'file' => $file,
        ];
    }
}
