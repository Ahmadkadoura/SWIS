<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompleteProfileRequest extends FormRequest
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
            'name.en' => ['required', 'string','min:4'],
            'name.ar' => ['string','min:4'],
            'password' => 'required|string|min:8|confirmed',
            'contact_email' => ['', 'string','email'],
            'phone' => ['required','phone:Auto',Rule::unique('users', 'phone')],
            'photo' => [ 'image' , 'mimes:jpeg,jpg,png,gif'],
        ];
    }
}
