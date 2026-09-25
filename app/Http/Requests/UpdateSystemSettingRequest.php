<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSystemSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isSuperAdmin();
    }

    public function rules(): array
    {
        return [
            'institute_name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'asset_prefix' => ['required', 'string', 'max:20'],
            'qr_prefix' => ['required', 'string', 'max:20'],
            'currency' => ['required', 'string', 'max:10'],
        ];
    }
}
