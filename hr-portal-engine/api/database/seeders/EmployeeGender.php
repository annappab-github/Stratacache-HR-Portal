<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeGender extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $empGender = array(
            array('empid' => 'SG002', 'gender' => 'female'),
            array('empid' => 'SG015', 'gender' => 'female'),
            array('empid' => 'SG017', 'gender' => 'female'),
            array('empid' => 'AUS003', 'gender' => 'female'),
            array('empid' => 'AUS006', 'gender' => 'female'),
            array('empid' => 'JP006', 'gender' => 'female'),
            array('empid' => 'PH001', 'gender' => 'female'),
            array('empid' => 'PH003', 'gender' => 'female'),
            array('empid' => 'IN016', 'gender' => 'female'),
            array('empid' => 'IN026', 'gender' => 'female'),
            array('empid' => 'IN034', 'gender' => 'female'),
            array('empid' => 'IN039', 'gender' => 'female'),
            array('empid' => 'IN041', 'gender' => 'female'),
            array('empid' => 'IN047', 'gender' => 'female'),
            array('empid' => 'IN052', 'gender' => 'female'),
        );
        foreach($empGender as $emp) {
            DB::table('employees')->where('empid', '=', $emp['empid'])->update(['gender' => $emp['gender']]);
        }
    }
}
