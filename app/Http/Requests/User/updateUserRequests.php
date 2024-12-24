<?php

namespace App\Http\Requests\User;

use App\Enums\userType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class updateUserRequests extends FormRequest
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
            'password' => [  'min:8'],
            'email' => [ 'string','email',Rule::unique('users', 'email')],
            'contact_email' => [ 'string','email',Rule::unique('users', 'contact_email')],
            'name.en' => [ 'string','min:4'],
            'name.ar' => [ 'string','min:4'],
            'phone' => [ 'string',Rule::unique('Users', 'phone'),'phone:sy,INTERNATIONAL'],
            'photo' => [ 'image'],
            'type' => [ new Enum(userType::class)],

        ];
    }
}
