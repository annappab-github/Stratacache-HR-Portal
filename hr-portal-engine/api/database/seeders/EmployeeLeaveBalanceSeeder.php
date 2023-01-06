<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee_leave_balance;
use Illuminate\Support\Facades\Log;
use App\Models\Leave_name;
use App\Models\Employees;
use Excel;
class EmployeeLeaveBalanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $excelPath = config('constants.leave_balance_excel_path');
        $excelData = Excel::toArray([],$excelPath);
        $leave_names = Leave_name::all();
        // Log::debug($leave_names);
        $excel_headers = $excelData[0][0];

        $empData = Employees::where('status', '=', 'active')
        ->get(['id', 'empid', 'gender']);

        foreach(array_slice($excelData[0],1) as $eachData) {
            foreach($empData as $emp) {
                if(trim($eachData[0]) == trim($emp['empid'])) {
                    if($eachData[0] != null && trim($eachData[0]) != '') {
                        for($i = 0; $i<count($eachData); $i++) {
                            foreach($leave_names as $leave) {
                                if(trim($leave['name']) == trim($excel_headers[$i])) {
                                    $insertflag = true;
                                    if ($leave['name'] == 'Maternity Leave Balance' && $emp['gender'] == 'male') {                                
                                        $insertflag = false;
                                    }
                                    if ($leave['name'] == 'Paternity Leave Balance' && $emp['gender'] == 'female') {
                                        $insertflag = false;
                                    }
                                    // Log:debug('if');
                                    $eachEmployee = Employee_leave_balance::create([
                                        'empid' => trim($eachData[0]),
                                        'balance' => ($insertflag ? trim($eachData[$i]) : ''),
                                        'leave_id' => $leave['id']
                                    ]);
                                }                               
                            }
                        }
                    }
                    break;
                }
            }            
        }
    }
}
