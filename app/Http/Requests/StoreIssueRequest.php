<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIssueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Assignment::class);
    }

    public function rules(): array
    {
        return [
            'inventory_id' => ['required', 'exists:inventories,id'],
            'assigned_to' => ['required', 'exists:users,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'issue_date' => ['required', 'date'],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
