<?php

namespace dnj\Ticket\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepartmentUpsertRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string'],
        ];
    }
}
