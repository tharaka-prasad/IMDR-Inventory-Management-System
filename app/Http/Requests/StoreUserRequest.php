<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $target = $this->route('user');

        return $target
            ? $this->user()->can('update', $target)
            : $this->user()->can('create', \App\Models\User::class);
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;
        $isUpdate = (bool) $userId;

        return [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => [$isUpdate ? 'nullable' : 'required', $isUpdate ? null : Password::defaults()],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }
}
