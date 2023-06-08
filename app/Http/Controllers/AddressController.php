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

        $addresses = Address::where('user_id', $id)->get();

        return response()->success($addresses);
    }
}
