<?php

namespace dnj\Ticket\Http\Requests;

use dnj\Ticket\Models\TicketMessage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'message_id' => ['required', 'sometimes', Rule::exists(TicketMessage::class, 'id')],
        ];
    }
}
