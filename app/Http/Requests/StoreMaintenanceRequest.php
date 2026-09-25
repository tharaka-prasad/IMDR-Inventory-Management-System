<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMaintenanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->route('maintenance')
            ? $this->user()->can('update', $this->route('maintenance'))
            : $this->user()->can('create', \App\Models\Maintenance::class);
    }

    public function rules(): array
    {
        return [
            'inventory_id' => ['required', 'exists:inventories,id'],
            'issue_title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'vendor' => ['nullable', 'string', 'max:255'],
            'repair_cost' => ['nullable', 'numeric', 'min:0'],
            'sent_date' => ['nullable', 'date'],
            'return_date' => ['nullable', 'date', 'after_or_equal:sent_date'],
            'status' => ['required', Rule::in(['pending', 'in_progress', 'completed', 'unrepairable'])],
        ];
    }
}
