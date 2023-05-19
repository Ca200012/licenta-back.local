<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class LoginRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:users,email',
            'password' => [
                'required',
                'string',
                Password::min(8)
                    ->letters()
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email-ul este obligatoriu.',
            'email.email' => 'Email-ul este invalid.',
            'email.exists' => 'Email-ul introdus nu a mai fost folosit la crearea unui cont.',

            'password.required' => 'Parola este obligatorie',
            'password.string' => 'Parola are un format invalid',
            'password.confirmed' => 'Parola si confirmarea parolei sunt diferite.',
            'password.min' => 'Parola trebuie sa aiba minim 8 caractere.',
            'password.letters' => 'Parola trebuie sa contina litere.',
        ];
    }
}
