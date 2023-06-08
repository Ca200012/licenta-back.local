<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressesRequest extends FormRequest
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
            "city_id" => [
                'required',
                'integer',
                'min:1',
                'exists:cities,city_id'
            ],
            "street" => [
                'required',
                'string',
                'min:2',
                'max:60',
            ],
            "street_number" => [
                'required',
                'integer',
                'min:1'
            ],
            "building" => [
                'sometimes',
                'string',
                'min:1',
                'max:10',
            ],
            "entrance" => [
                'sometimes',
                'string',
                'min:1',
                'max:10',
            ],
            "apartment" => [
                'sometimes',
                'integer',
                'min:1'
            ],
            "postal_code" => [
                'required',
                'string',
                'min:6',
                'max:6',
            ],
        ];
    }
}
