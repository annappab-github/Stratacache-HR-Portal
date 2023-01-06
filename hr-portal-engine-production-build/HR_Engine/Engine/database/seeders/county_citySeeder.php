<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cities;
use App\Models\Countries;

class county_citySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Seed Countries
        $counties = array('Australia', 'China', 'Hong Kong', 'India', 'Japan', 'Malaysia', 'Philippines', 'Singapore');
        foreach($counties as $eachCountry) {
            Countries::create([
                'country' => $eachCountry,
            ]);
        }


        // Seed Cities
        $counties_cities = array(
            array('country' => 'Australia', 'cities' => array('Canberra', 'Sydney')),
            array('country' => 'China', 'cities' => array('China')),
            array('country' => 'Hong Kong', 'cities' => array('Hong Kong')),
            array('country' => 'India', 'cities' => array('Bangalore', 'Delhi', 'Mumbai')),
            array('country' => 'Japan', 'cities' => array('Japan')),
            array('country' => 'Malaysia', 'cities' => array('Malaysia')),
            array('country' => 'Philippines', 'cities' => array('Philippines')),
            array('country' => 'Singapore', 'cities' => array('Singapore'))
        );

        foreach($counties_cities as $eachData) {
            foreach($eachData['cities'] as $city) {
                $country = Countries::where('country', '=', $eachData['country'])->get();
                if($country->count()>0) {
                    Cities::create([
                        'country_id' => $country[0]->id,
                        'city' => $city
                    ]);
                }
            }
        }
    }
}
