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
                'nullable',
                'string',
                'min:1',
                'max:10',
            ],
            "entrance" => [
                'nullable',
                'string',
                'min:1',
                'max:10',
            ],
            "apartment" => [
                'nullable',
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
        // $rules = [
        //     "county_id" => [
        //         'required',
        //         'integer',
        //         'min:1',
        //         'exists:counties,county_id'
        //     ],
        //     "city_id" => [
        //         'required',
        //         'integer',
        //         'min:1',
        //         'exists:cities,city_id'
        //     ],
        //     "street" => [
        //         'required',
        //         'string',
        //         'min:2',
        //         'max:60',
        //     ],
        //     "street_number" => [
        //         'required',
        //         'integer',
        //         'min:1'
        //     ],
        //     "postal_code" => [
        //         'required',
        //         'string',
        //         'min:6',
        //         'max:6',
        //     ],
        // ];

        // // Add conditional validation rules for building, entrance, and apartment fields
        // if ($this->input('building') !== null) {
        //     $rules['building'] = [
        //         'string',
        //         'min:1',
        //         'max:10',
        //     ];
        // }

        // if ($this->input('entrance') !== null) {
        //     $rules['entrance'] = [
        //         'string',
        //         'min:1',
        //         'max:10',
        //     ];
        // }

        // if ($this->input('apartment') !== null) {
        //     $rules['apartment'] = [
        //         'integer',
        //         'min:1'
        //     ];
        // }

        // return $rules;
    }
}
