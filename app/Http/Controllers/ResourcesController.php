<?php

namespace App\Http\Controllers;

use App\Http\Requests\CitiesRequest;
use App\Models\Resources\City;
use App\Models\Resources\County;

class ResourcesController extends Controller
{
    public function getCounties()
    {
        $counties = County::select("county_id", "name")->orderBy("name")->get();

        return response()->success($counties);
    }

    public function getCitiesBySearch(CitiesRequest $request)
    {
        try {
            $data = $request->validated();
            $search = strtolower($data['search']);

            $cities = City::select("city_id", "name")
                ->distinct("name")
                ->whereRaw("remove_accents(LOWER(name)) LIKE remove_accents(?)", ["%$search%"])
                ->where("county_id", "=", $data["county_id"])
                ->orderBy("name")
                ->limit(10)
                ->get();

            return response()->success($cities);
        } catch (\Exception $ex) {
            return response()->error("Am intampinat o eroare");
        }
    }
}
