<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('product'));
    }

    public function rules(): array
    {
        $productId = $this->route('product')->id;
        return [
            'sku' => ['required', 'string', 'max:50', Rule::unique('products', 'sku')->ignore($productId)],
            'barcode' => ['nullable', 'string', 'max:50', Rule::unique('products', 'barcode')->ignore($productId)],
            'name' => 'required|string|max:200',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'description' => 'nullable|string',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'is_taxable' => 'boolean',
            'track_stock' => 'boolean',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }
}
