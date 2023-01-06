<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use File;
use App\Models\holidaylist;
class HolidaylistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $json = File::get("database/data/data.json");
        $holidays = json_decode($json);
        foreach ($holidays->holiday as $key => $region) {
            // Log::debug($region->regionname);
            // Log::debug($region->holidaydetails);
            foreach($region->holidaydetails as $key1 => $value){
                // Log::debug($value->date);
                holidaylist::create([
                    "region" => $region->regionname,
                    "holidaydate" => $value->date,
                    "holidaytype" => $value->type,
                    "holidayname" => $value->holidayname
                ]);

            }
        }
        
    }
}
