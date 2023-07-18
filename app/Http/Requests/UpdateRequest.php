<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
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
        $id = Auth::id();
        return [
            'first_name' => 'required|string|max:55',
            'last_name' => 'required|string|max:55',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($id, 'user_id')
            ],
            'phone_number' => [
                'required',
                'regex:/^[0-9]{10}$/',
                Rule::unique('users', 'phone_number')->ignore($id, 'user_id')
            ],
            'date_of_birth' => 'sometimes|required|date:Y-m-d'
        ];
    }

    // public function messages(): array
    // {
    //     return [
    //         'first_name.required' => 'Prenumele este obligatoriu',
    //         'first_name.string' => 'Prenumele are un format invalid.',
    //         'first_name.max' => 'Prenumele trebuie sa aiba maxim 55 de caractere.',

    //         'last_name.required' => 'Numele este obligatoriu',
    //         'last_name.string' => 'Numele are un format invalid.',
    //         'last_name.max' => 'Numele trebuie sa aiba maxim 55 de caractere.',

    //         'email.required' => 'Email-ul este obligatoriu.',
    //         'email.email' => 'Email-ul este invalid.',
    //         'email.unique' => 'Email-ul introdus a fost deja folosit.',

    //         'phone_number.required' => 'Numarul de telefon este obligatoriu.',
    //         'phone_number.regex' => 'Numarul de telefon are un format invalid.',
    //         'phone_number.unique' => 'Numarul de telefon introdus a fost deja folosit.',

    //         'date_of_birth.required' => 'Data nasterii este obligatorie',
    //         'date_of_birth.date' => 'Data nasterii are un format invalid'
    //     ];
    // }
}
