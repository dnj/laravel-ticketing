<?php

namespace dnj\Ticket\Http\Requests;

use dnj\Ticket\Rules\AttachmentValidation;
use Illuminate\Foundation\Http\FormRequest;

class TicketMessageUpsertRequest extends FormRequest
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
            'message' => ['required', 'string'],
            'attachments.*' => [
                'sometimes', 'required', new AttachmentValidation(),
            ],
        ];
    }
}
