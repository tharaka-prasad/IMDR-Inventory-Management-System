<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Inventory::class);
    }

    public function rules(): array
    {
        $inventoryId = $this->route('inventory')?->id;

        return [
            // Asset Information
            'asset_type' => ['required', Rule::in(['fixed_asset', 'it_asset', 'consumable'])],
            'category_id' => ['required', 'exists:categories,id'],
            'location_id' => ['required', 'exists:locations,id'],
            'item_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'condition' => ['required', Rule::in(['new', 'good', 'fair', 'damaged', 'disposed'])],
            'status' => ['required', Rule::in(['active', 'in_maintenance', 'disposed', 'inactive'])],

            // Purchase Information
            'unit_cost' => ['required', 'numeric', 'min:0'],
            'supplier' => ['nullable', 'string', 'max:255'],
            'invoice_number' => ['nullable', 'string', 'max:255'],
            'purchase_date' => ['nullable', 'date'],
            'received_date' => ['nullable', 'date'],
            'payment_method' => ['nullable', 'string', 'max:100'],
            'payment_date' => ['nullable', 'date'],

            // Warranty
            'warranty_start' => ['nullable', 'date'],
            'warranty_end' => ['nullable', 'date', 'after_or_equal:warranty_start'],

            // IT Details (optional block, only relevant when asset_type = it_asset)
            'it_details' => ['nullable', 'array'],
            'it_details.device_name' => ['nullable', 'string', 'max:255'],
            'it_details.hostname' => ['nullable', 'string', 'max:255'],
            'it_details.cpu' => ['nullable', 'string', 'max:255'],
            'it_details.ram' => ['nullable', 'string', 'max:255'],
            'it_details.storage' => ['nullable', 'string', 'max:255'],
            'it_details.gpu' => ['nullable', 'string', 'max:255'],
            'it_details.operating_system' => ['nullable', 'string', 'max:255'],
            'it_details.mac_address' => ['nullable', 'string', 'max:255'],
            'it_details.ip_address' => ['nullable', 'string', 'max:255'],
            'it_details.monitor_size' => ['nullable', 'string', 'max:100'],
            'it_details.printer_type' => ['nullable', 'string', 'max:100'],
            'it_details.printer_ip' => ['nullable', 'string', 'max:255'],
            'it_details.software_installed' => ['nullable', 'string'],
            'it_details.license_reference' => ['nullable', 'string', 'max:255'],
            'it_details.antivirus' => ['nullable', 'string', 'max:255'],
            'it_details.it_notes' => ['nullable', 'string'],

            // Depreciation (optional block)
            'depreciation' => ['nullable', 'array'],
            'depreciation.method' => ['nullable', Rule::in(['SLM', 'WDV'])],
            'depreciation.useful_life' => ['nullable', 'integer', 'min:1'],
            'depreciation.disposal_date' => ['nullable', 'date'],
            'depreciation.disposal_reason' => ['nullable', 'string', 'max:255'],
            'depreciation.disposal_value' => ['nullable', 'numeric', 'min:0'],
            'depreciation.remarks' => ['nullable', 'string'],
        ];
    }
}
