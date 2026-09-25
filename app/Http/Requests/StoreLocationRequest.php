<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->route('location')
            ? $this->user()->can('update', $this->route('location'))
            : $this->user()->can('create', \App\Models\Location::class);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'building' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
        ];
    }
}
