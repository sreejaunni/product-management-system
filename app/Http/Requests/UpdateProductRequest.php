<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $this->route('id'),
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'category_ids' => 'sometimes|array',
            'category_ids.*' => 'exists:categories,id',
            'is_active' => 'nullable|boolean',
             'images' => 'nullable|array',
             'images.*' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ];
    }
}
