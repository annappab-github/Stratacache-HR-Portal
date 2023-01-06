<?php

namespace App\Http\Controllers;

use App\Models\Cities;
use Illuminate\Http\Request;

class CitiesController extends Controller
{
    const GET_CITIES_AND_COUNTRIES = [self::class, 'getCitiesAndCountries'];

    public function getCitiesAndCountries() {
        $cityCounty = Cities::from('cities as ct')
        ->join('countries as cn', 'ct.country_id', '=', 'cn.id')
        ->get(['ct.id as city_id', 'ct.country_id as country_id', 'ct.city', 'cn.country as location']);
        return response()->json(
            $cityCounty
        , 200);
    }
}
