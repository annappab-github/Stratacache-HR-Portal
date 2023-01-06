<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LeaveAccrualRules;

class LeaveAccrualRulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $leaveRules = array(
            array('region'=> 'Australia', 'accrual' => 'yearly', 'leave_type' => 1, 'carry_forword' => 'yes', 'max_limit' => 'NA'),
            array('region'=> 'Australia', 'accrual' => 'yearly', 'leave_type' => 2, 'carry_forword' => 'yes', 'max_limit' => 'NA'),
            array('region'=> 'Hong Kong', 'accrual' => 'yearly', 'leave_type' => 1, 'carry_forword' => 'yes', 'max_limit' => 'NA'),
            array('region'=> 'India', 'accrual' => 'yearly', 'leave_type' => 1, 'carry_forword' => 'yes', 'max_limit' => 'NA'),
            array('region'=> 'Japan', 'accrual' => 'yearly', 'leave_type' => 1, 'carry_forword' => 'yes', 'max_limit' => 'NA'),
            array('region'=> 'Malaysia', 'accrual' => 'yearly', 'leave_type' => 1, 'carry_forword' => 'yes', 'max_limit' => 'NA'),
            array('region'=> 'Singapore', 'accrual' => 'yearly', 'leave_type' => 1, 'carry_forword' => 'yes', 'max_limit' => 'NA'),
        );

        foreach($leaveRules as $rule) {
            $add_data = array();
            foreach($rule as $key=>$value) {
                $add_data[$key] = $value;
            }
            LeaveAccrualRules::create($add_data);
        }
    }
}
