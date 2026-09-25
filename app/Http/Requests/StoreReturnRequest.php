<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('return', \App\Models\Assignment::class);
    }

    public function rules(): array
    {
        return [
            'assignment_id' => ['required', 'exists:assignments,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'return_date' => ['required', 'date'],
            'condition' => ['required', Rule::in(['new', 'good', 'fair', 'damaged'])],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
