<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressesRequest;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function postAddress(AddressesRequest $request)
    {
        $data = $request->validated();
        $id = Auth::id();
        $address = Address::create([
            'user_id' => $id,
            'county_id' => $data['county_id'],
            'city_id' => $data['city_id'],
            'street' => $data['street'],
            'street_number' => $data['street_number'],
            'building' => $data['building'],
            'entrance' => $data['entrance'],
            'apartment' => $data['apartment'],
            'postal_code' => $data['postal_code'],
        ]);

        return response()->success($address);
    }

    public function getAddresses()
    {
        $id = Auth::id();

        $addresses = Address::where('user_id', $id)
            ->select('address_id', 'street', 'street_number', 'building', 'entrance', 'apartment', 'postal_code')
            ->get()
            ->map(function ($address) {
                $parsedAddress = $address->street . ', No. ' . $address->street_number;

                if ($address->building) {
                    $parsedAddress .= ', Bld. ' . $address->building;
                }

                if ($address->entrance) {
                    $parsedAddress .= ', Ent. ' . $address->entrance;
                }

                if ($address->apartment) {
                    $parsedAddress .= ', Ap. ' . $address->apartment;
                }

                $parsedAddress .= ', ' . $address->postal_code;

                $address->value = $parsedAddress;

                return $address->only('address_id', 'value');
            });

        return response()->success($addresses);
    }

    public function getAddressData($address_id)
    {
        $address = Address::select(
            'addresses.address_id',
            'addresses.apartment',
            'addresses.building',
            'addresses.entrance',
            'addresses.postal_code',
            'addresses.street',
            'addresses.street_number',

            'cities.name as city_name',
            'counties.name as county_name'
        )
            ->join('cities', 'addresses.city_id', '=', 'cities.city_id')
            ->join('counties', 'addresses.county_id', '=', 'counties.county_id')
            ->findOrFail($address_id);
        return response()->success($address);
    }
}
