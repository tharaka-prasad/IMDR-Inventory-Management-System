<?php

namespace App\Http\Requests;

class UpdateInventoryRequest extends StoreInventoryRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('inventory'));
    }
}
