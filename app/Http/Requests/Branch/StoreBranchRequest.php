<?php

namespace App\Http\Requests\Branch;

use Illuminate\Foundation\Http\FormRequest;

class StoreBranchRequest extends FormRequest
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
            'name.en'      => 'required|string|min:4',
            'name.ar'      => 'string|min:4',
            'parent_id' => 'nullable|integer|exists:branches,id',
            'address.en'   => 'required_without:address.ar|string',
            'address.ar'   => 'required_without:address.en|string',
        ];
    }
}
