<?php

namespace dnj\Ticket\Rules;

use dnj\Ticket\Models\TicketAttachment;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule as ValidateRule;

class AttachmentValidation implements Rule
{
    public function passes($attribute, $value)
    {
        $input = ['file' => $value];
        $rule = [
            'file' => is_int($value) ?
                ValidateRule::exists(TicketAttachment::class, 'id') :
                array_merge(['file'], config('ticket.attachment_rules')),
        ];

        return Validator::make($input, $rule)->passes();
    }

    public function message()
    {
        return 'The attachments not valid.';
    }
}
