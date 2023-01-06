<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\Log;
use File;
use App\Models\Workingsaturdays;
use Illuminate\Database\Seeder;

class WorkingsaturdaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $json = File::get("database/data/saturday.json");
        $holidays = json_decode($json);
        foreach ($holidays->saturday as $key => $region) {
            // Log::debug($region->regionname);
            foreach($region->workingsat as $key1 => $value){
                // Log::debug($value->date);
                Workingsaturdays::create([
                    "region" => $region->regionname,
                    "holidaydate" => $value->date,
                ]);

            }
        }
    }
}
