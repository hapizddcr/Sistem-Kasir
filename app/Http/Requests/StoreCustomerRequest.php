<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Customer::class);
    }

    public function rules(): array
    {
        $customerId = $this->route('customer')?->id;
        return [
            'name' => 'required|string|max:150',
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('customers', 'phone')->ignore($customerId)],
            'email' => ['nullable', 'email', 'max:100', Rule::unique('customers', 'email')->ignore($customerId)],
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'gender' => 'nullable|in:male,female',
            'birth_date' => 'nullable|date',
            'membership_id' => 'nullable|exists:memberships,id',
            'is_active' => 'boolean',
        ];
    }
}
