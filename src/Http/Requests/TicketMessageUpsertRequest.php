<?php

namespace dnj\Ticket\Http\Requests;

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
            'message' =>  ['required', 'string'],
        ];
    }
}
