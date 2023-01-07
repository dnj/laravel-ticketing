<?php

namespace dnj\Ticket\Http\Requests;

use dnj\Ticket\Enums\TicketStatus;
use dnj\Ticket\ModelHelpers;
use dnj\Ticket\Models\Department;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TicketUpdateRequest extends FormRequest
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
            'title' => $this->isTitleRequire() ? ['sometimes', 'required', 'string'] : [],
            'department_id' => ['sometimes', 'required', Rule::exists(Department::class, "id")],
            'client_id' => ['sometimes', 'required', Rule::exists($this->getUserModel())],
            'status' => ['sometimes', 'required', Rule::enum(TicketStatus::class)],
        ];
    }

}
