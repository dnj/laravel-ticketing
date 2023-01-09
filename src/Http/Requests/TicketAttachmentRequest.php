<?php

namespace dnj\Ticket\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketAttachmentRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'attachments.*' => config('ticket.attachment_rules'),
        ];
    }
}
