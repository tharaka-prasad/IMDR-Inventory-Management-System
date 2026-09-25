<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only Super Admin can reach this request at all (also gated in the
        // controller and by policy), enforcing "Super Admin only creates Admins".
        return $this->user()->isSuperAdmin();
    }

    public function rules(): array
    {
        $userId = $this->route('admin')?->id;
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
