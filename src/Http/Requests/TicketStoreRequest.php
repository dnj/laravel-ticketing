<?php

namespace dnj\Ticket\Http\Requests;

use dnj\Ticket\ModelHelpers;
use dnj\Ticket\Models\Department;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TicketStoreRequest extends FormRequest
{
    use ModelHelpers;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string,mixed>
     */
    public function rules(): array
    {
        return [
            'title' => $this->isTitleRequire() ? ['required', 'string'] : [],
            'department_id' => ['required', Rule::exists(Department::class, "id")],
            'client_id' => ['sometimes', 'required', Rule::exists($this->getUserModel())],
            'message' => ['required'],
        ];
    }

}
