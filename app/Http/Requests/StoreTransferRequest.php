<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('transfer', \App\Models\Assignment::class);
    }

    public function rules(): array
    {
        return [
            'from_assignment_id' => ['required', 'exists:assignments,id'],
            'to_user_id' => ['required', 'exists:users,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'transfer_date' => ['required', 'date'],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
