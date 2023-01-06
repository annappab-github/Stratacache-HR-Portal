<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employees;
use App\Models\LeaveEntitlementTable;
use Illuminate\Support\Facades\Log;
use DateTime;

class LeaveEntitlementTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $leaveData = array(
            array('location'=> 'India', 'annual_leave' => '14', 'sick_leave' => '12', 'optional_leave' => '2', 'compensatory_leave' => '14', 'marriage_leave' => '7', 'bereavement_leave' => '7', 'maternity_leave' => '182', 'paternity_leave' => '5'),
            array('location'=> 'Singapore', 'annual_leave' => 'leavetracker', 'sick_leave' => '14', 'compensatory_leave' => '14', 'marriage_leave' => '7', 'bereavement_leave' => '2', 'maternity_leave' => '112', 'paternity_leave' => '14', 'childcare_leave' => '6', 'national_service_leave' => '', 'hospitalization_leave' => '60'),
            array('location'=> 'Australia', 'annual_leave' => '20', 'sick_leave' => '10', 'compensatory_leave' => '14', 'marriage_leave' => '7', 'bereavement_leave' => '2', 'maternity_leave' => '126', 'paternity_leave' => '14', 'carers_leave' => '5'),
            array('location'=> 'Japan', 'annual_leave' => 'leavetracker', 'sick_leave' => '5', 'compensatory_leave' => '14', 'marriage_leave' => '7', 'bereavement_leave' => '3', 'maternity_leave' => '98'),
            array('location'=> 'China', 'annual_leave' => '14', 'sick_leave' => '12', 'compensatory_leave' => '14', 'marriage_leave' => '0', 'bereavement_leave' => '0', 'maternity_leave' => '0', 'paternity_leave' => '0'),
            array('location'=> 'Hong Kong', 'annual_leave' => '20', 'sick_leave' => '24', 'compensatory_leave' => '14', 'marriage_leave' => '7', 'bereavement_leave' => '2'),
            array('location'=> 'Malaysia', 'annual_leave' => '14', 'sick_leave' => '14', 'compensatory_leave' => '14', 'marriage_leave' => '7', 'bereavement_leave' => '2'),
            array('location'=> 'Philippines', 'annual_leave' => '14', 'sick_leave' => '5', 'compensatory_leave' => '14', 'marriage_leave' => '7', 'bereavement_leave' => '2')
        );

        $singapore_annual_leave = array(
            array('empids' => array('SG001','SG002', 'SG006'), 'leaves' => '21'),
            array('empids' => array('SG005', 'SG015'), 'leaves' => '18'),
            array('empids' => array(), 'leaves' => '14')           
        );

        // Japan initial Annual leaves 14, increase 1 every 2 years

        $empData = Employees::from('employees as em')
        ->join('users as ur', 'ur.employee_id', '=', 'em.empid')
        ->where('em.status', '=', 'active')
        ->where('em.employement_type', '=', 'Full Time Employee')
        ->get(['em.id', 'em.empid', 'em.gender', 'em.start_Date', 'em.location']);
        foreach($empData as $emp) {
            foreach($leaveData as $region_leave) {
                if($emp['location'] ==  $region_leave['location']) {
                    $add_data = array('empid' => trim($emp['empid']));
                    foreach($region_leave as $key=>$value){
                        if($key != 'location' && $key != 'annual_leave') {
                            if ($emp['gender'] == 'male' && $key == 'maternity_leave') {
                                // $add_data[$key] = '';
                            } else if($emp['gender'] == 'female' && $key == 'paternity_leave') {
                                // $add_data[$key] = '';
                            } else {
                                $add_data[$key] = $value;
                            }
                        }
                        if($key != 'location' && $key == 'annual_leave' && $value == 'leavetracker') {
                            if($emp['location'] == 'Singapore') {
                                $loopflag = false;
                                foreach($singapore_annual_leave as $sg_annual_leave) {
                                    if(count($sg_annual_leave['empids']) > 0) {
                                        foreach($sg_annual_leave['empids'] as $sg_empid) {
                                            if ($emp['empid'] == $sg_empid) {
                                                $add_data[$key] = $sg_annual_leave['leaves'];
                                                $loopflag = true;
                                                break;
                                            }
                                        }
                                    } else {
                                        $loopflag = true;
                                        $add_data[$key] = $sg_annual_leave['leaves'];
                                    }
                                    if ($loopflag) {
                                        break;
                                    }
                                }
                            } else if($emp['location'] == 'Japan'){
                                $add_data[$key] = $value;
                                $d1 = new DateTime($emp['start_Date']);
                                $d2 = new DateTime();
                                $diff = $d2->diff($d1)->y;
                                $initial_annual_leave = 14;
                                $add_data[$key] = ($initial_annual_leave + floor($diff/2));
                            }
                        } else if($key != 'location' && $key == 'annual_leave') {
                            $add_data[$key] = $value;
                        }
                    }
                    LeaveEntitlementTable::create($add_data);
                }
            }
        }
    }
}
