<?php

namespace dnj\Ticket\Http\Requests;

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
            'attachments.*' => array_merge(['required', 'file'], config('ticket.attachment_rules')),
        ];
    }
}
