<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Supplier::class);
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|max:20|unique:suppliers,code',
            'name' => 'required|string|max:150',
            'contact_person' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'npwp' => 'nullable|string|max:30',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }
}
