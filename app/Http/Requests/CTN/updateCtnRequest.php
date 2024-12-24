<?php

namespace App\Http\Requests\CTN;

use Illuminate\Foundation\Http\FormRequest;

class updateCtnRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'warehouse_item_id' => 'exists:warehouse_items,id',
            'item_id' => 'exists:items,id',
            'quantity' => 'integer|min:1',
            'CTN' => 'string|max:255',

        ];
    }
}
