<?php

namespace App\Http\Requests\User;

use App\Enums\userType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class storeUserRequests extends FormRequest
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
            //'password' => ['required', 'min:8'],
            //'email' => ['required', 'string', 'email', Rule::unique('users', 'email')],
            'contact_email' =>['required', 'string', 'email', Rule::unique('users', 'contact_email')],
            'name.en' => ['required', 'string','min:4'],
            'name.ar' => ['string','min:4'],
            'phone' => ['required',Rule::unique('Users', 'phone'),'phone:sy,INTERNATIONAL'],
//              'photo' => [ 'required', 'image'],
            'type' => ['required', new Enum(userType::class)],
        ];
    }
}
