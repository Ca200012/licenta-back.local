<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CitiesRequest extends FormRequest
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
            "county_id" => [
                'required',
                'integer',
                'min:1',
                'exists:counties,county_id'
            ],
            "search" => [
                'required',
                'string',
                'min:2',
                'regex:/^[\pL\s]+$/u'
            ]
        ];
    }
}
