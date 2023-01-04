<?php

namespace dnj\Ticket\Http\Requests;

use dnj\Ticket\Enums\TicketStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TicketUpsertRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => 'required|string',
            'department_id' => 'required|exists:departments,id',
            'client_id' => 'sometimes|required|exists:users,id',
            'message' => [Rule::when(request()->isMethod('POST'), 'required')],
            'status' =>  [
                'declined_if:client_id,' . auth()->user()->id,
                Rule::when(request()->isMethod('PUT'), ['required', Rule::enum(TicketStatus::class)])
            ],
        ];
    }


    protected function prepareForValidation()
    {
        if (is_null($this->client_id)) {
            $this->request->remove('client_id');
        }
    }
}
