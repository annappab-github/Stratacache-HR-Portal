<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Excel;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Log;
use App\Models\create_apply_leave_table;
use App\Models\Employee_leave_balance;
use App\Models\Leave_name;
class ApplyleaveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function formatDate($excelDate) {
        $UNIX_DATE = ((int)$excelDate - 25569) * 86400;
        $formattedDate = gmdate("d-m-Y", $UNIX_DATE);
        return $formattedDate;
    }

    public function handleDuration($startDate, $endDate){
        $start = strtotime($startDate);
        $end  = strtotime($endDate);
        $datediff   = $end - $start;
        $duration = round($datediff / (60 * 60 * 24)) + 1;
        return $duration;
    }

    public function getLeaveTypeAndLeaveIdByLeaveName($data){
        $convertLeaveName = '';
        $typeNameId = array();
        if($data == 'Earned')
            $convertLeaveName = 'Annual Leave Balance';
        else if($data == 'Sick')
            $convertLeaveName = 'Sick Leave Balance';
        else if($data == 'Compensatory')
            $convertLeaveName = 'Compensatory Leave Balance';
        else if($data == 'Optional')
            $convertLeaveName = 'Optional Leave Balance';
        else if($data == 'Childcare')
            $convertLeaveName = 'Childcare Leave Balance';
        
        $leaveType = Leave_name::from('leave_names as na')
        ->where('na.name', $convertLeaveName)
        ->get(['na.name', 'na.id']);

        for ($i=0; $i < count($leaveType) ; $i++) { 
            array_push($typeNameId, $leaveType[$i]->name);
            array_push($typeNameId, $leaveType[$i]->id);
        }
        return $typeNameId;
    }

    public function run()
    {   
        $years = array('2022', '2023');
        foreach($years as $year) {
            $excelPath = config('constants.leave_list_base_path')."/".$year."/".config('constants.leave_list_excel_name');
            $excelData = Excel::toArray([],$excelPath);
            foreach(array_slice($excelData[0],1) as $eachData) {
                $startDate = Carbon::parse($this->formatDate($eachData[4]));
                $endDate = Carbon::parse($this->formatDate($eachData[5]));
    
                if($eachData[1] != null) {
                    $leaveNameId = $this->getLeaveTypeAndLeaveIdByLeaveName($eachData[2]);
                    $eachEmployee = create_apply_leave_table::create([
                        'empid' => trim($eachData[0]),
                        'leave_type' => trim(str_replace('Balance', '', $leaveNameId[0])),
                        'leave_id' => $leaveNameId[1],
                        'duration_type' => trim($eachData[6]),
                        'reason' => $eachData[3],
                        'num_of_days' => $this->handleDuration($startDate, $endDate),
                        'start_date' => $startDate,
                        'status' => 'Approved',
                        'end_date' => $endDate
                    ]);
                }
            }
        }
    }
}
