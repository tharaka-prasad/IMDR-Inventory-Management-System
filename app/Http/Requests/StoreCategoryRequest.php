<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->route('category')
            ? $this->user()->can('update', $this->route('category'))
            : $this->user()->can('create', \App\Models\Category::class);
    }

    public function rules(): array
    {
        $categoryId = $this->route('category')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', Rule::unique('categories', 'code')->ignore($categoryId)],
            'asset_type' => ['required', Rule::in(['fixed_asset', 'it_asset', 'consumable'])],
            'description' => ['nullable', 'string'],
        ];
    }
}
