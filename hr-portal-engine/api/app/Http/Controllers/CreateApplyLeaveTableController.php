<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\create_apply_leave_table;
use App\Models\Employee_leave_balance;
use App\Models\Leave_name;
use App\Models\Employees;
use App\Models\holidaylist;
use App\Models\Workingsaturdays;
use DateTime;
use DateInterval;
use DatePeriod;
use DB;
use Carbon\Carbon;

class CreateApplyLeaveTableController extends Controller
{
    const GET_EMPLOYEE_REPORTING_MANAGER = [self::class, 'getDirectReporteeDetailsByManagerId'];
    const UPLOAD_MEDICAL_ATTACHMENT = [self::class, 'uploadEmployeeMedicalAttachment'];
    const APPROVE_APPLIED_LEAVE = [self::class, 'aprroveOrRejectEmployeeAppliedLeave'];

    public function employeeLeaveApply(Request $request){
            
            $from  = strtotime($request->from);
            $to = strtotime($request->to);
            $region_name = substr($request->empId,0,2);
            $datediff = $this->handleHolidaysAndWeekends($request->from, $request->to, $region_name);

            //check start end ,end date validation
            $start = new DateTime($request->from);
            $end = new DateTime($request->to);
            $diff = date_diff($start, $end);
            $finalDiff = $diff->format("%R%a");

            if($finalDiff >= 0){
                 //Half day leave condition
                if($request->duration != 'Full Day' and $datediff == 1) {
                    $datediff = 0.5;
                }

                if($this->checkIfValidDateRange($request->empId, $request->from, $request->to, 0)) {
                    return response()->json('You already have one or more approved/applied leaves from the selected date range.', 203);
                }

                if($request->leaveid != 13) {
                    $currentLeaveBalance = $this->getEmployeeCurrentLeaveBalance($request->empId, $request->leaveid);
                    $pendingApprovalBalance = $this->getPendingLeaveDaysCountByEmpIdAndLeaveId($request->empId, $request->leaveid);

                    if((float)$datediff > ((float)$currentLeaveBalance[0]->balance - (float)$pendingApprovalBalance)) {
                        return response()->json('You have insufficient leave balance.', 203);
                    }
                }

                try {
                    DB::beginTransaction();
                    $leavedata = new create_apply_leave_table;
                    $leavedata->empid = $request->empId;
                    $leavedata->leave_type = $request->leaveCat;
                    $leavedata->leave_id = $request->leaveid;
                    $leavedata->duration_type = $request->duration;
                    $leavedata->reason = $request->reason;
                    if($request->autoApproved) {
                        $leavedata->comments = $request->reason;
                    }
                    $leavedata->num_of_days = $datediff;
                    $leavedata->start_date = $request->from;
                    $leavedata->end_date = $request->to;
                    $leavedata->status = $request->status;
                    $leavedata-> save();

                    if($request->leaveid != 13 and $request->autoApproved) {
                        $this->autoApproveLeave($request, $leavedata);
                    } 

                    DB::commit();
                    
                } catch (\Exception $e) {   
                    DB::rollBack();
                    return response()->json('error', 500);
                }

                //It has been done to complete the db transaction before sending email
                if($request->autoApproved) {
                    $leaveDetails = [
                        'empid' => $leavedata->empid,
                        'leaveId' => $leavedata->leave_id,
                        'totalDays' => $leavedata->num_of_days,
                        'leaveType' => $leavedata->leave_type,
                        'status' => $leavedata->status,
                        'comments' => $leavedata->reason
                    ];
                    $request->merge(["type" => "Approved Leave", "subject" => "Leave Application", "fromAdd" => "notifications@theportal.scala-apac.org", "leaveDetails" => $leaveDetails]);
                    try {
                        app('App\Http\Controllers\EmailController')->sendEmail($request);
                    } catch (\Exception $e) {
                        return response()->json($leavedata, 200);
                    }
                }

                if(!($request->autoApproved)) {
                $request->merge(["type" => "Apply Leave", "subject" => "Leave Application", "fromAdd" => "notifications@theportal.scala-apac.org"]);
                    try {
                        app('App\Http\Controllers\EmailController')->sendEmail($request);
                    } catch (\Exception $e) {
                        return response()->json($leavedata, 200);
                    }
                }

                return response()->json($leavedata, 200);

                } 
            else {
                return response()->json('Select valid dates', 203);
            }
           
    }

    public function handleHolidaysAndWeekends($from, $to, $region_name){
        // Log::debug($from);
        $start = new DateTime($from);
        $end = new DateTime($to);
        
        $end->modify('+1 day');

        $interval = $end->diff($start);

        // total days
        $days = $interval->days;

        // create an iterateable period of date (P1D equates to 1 day)
        $period = new DatePeriod($start, new DateInterval('P1D'), $end);
        $holidayData = holidaylist::from('holidaylists as hl')
        ->where('region', '=', $region_name)
        ->where('holidaytype', 'like', 'public%')
        ->get(['hl.holidaydate']);

        $dateArr = [];
        foreach($holidayData as $data){
            array_push($dateArr, $data->holidaydate);
        }
        // Log::debug( $dateArr);
        $weekendData = Workingsaturdays::from('workingsaturdays as ws')
        ->where('region', '=', $region_name)
        ->get(['ws.holidaydate']);
        $workingSat = [];
        foreach($weekendData as $data){
            array_push($workingSat, $data->holidaydate);
        }
        // best stored as array, so you can add more than one
        $holidays = array('2022-12-01');
        $allSaturday = [];
        
        foreach($period as $dt) {
            $curr = $dt->format('D');
            // substract if Saturday or Sunday
            if ($curr == 'Sat' || $curr == 'Sun') {
                if($curr == 'Sat'){
                    if(in_array($dt->format('Y-m-d'), $workingSat)){
                        // Log::debug($dt->format('Y-m-d')); 

                    } else {
                        $days--;
                    }
                } else {
                    $days--;
                }
            }

            // (optional) for the updated question
            elseif (in_array($dt->format('Y-m-d'), $dateArr)) {
            $days--;
            }
        }

        // Log::debug($holidays);
        // Log::debug($days);

        return $days;
    }

    public function getLeaveIdByLeaveType(Request $request) {
        
        $empLeaveData = Leave_name::from('leave_names as na')
        ->where('na.name', '=', $request->leaveType)
        ->get(['na.id']);

        return response()->json([
            'empLeaveData' => $empLeaveData[0]
        ], 200);
    }

    public function delete(Request $request, $id){
        $existingLeave = create_apply_leave_table::findOrFail($id);

        //Check if record was not modified between last read and current write
        $request_record_timestamp = Carbon::parse($request->updatedAt)->format('Y-m-d H:i:s');
        $db_record_timestamp = $existingLeave != null ? $existingLeave->updated_at : '';
        if($db_record_timestamp == '' || $request_record_timestamp != $db_record_timestamp) {
            return response()->json('This record has been updated by another user. Kindly refresh the page before trying again.', 203);
        }

        $existingLeave->delete();

        return response()->json('ok', 200);
    }

    public function update(Request $request, $id)
    {
        $existingLeave = create_apply_leave_table::find($id);

        //Check if record was not modified between last read and current write
        $request_record_timestamp = Carbon::parse($request->updatedAt)->format('Y-m-d H:i:s');
        $db_record_timestamp = $existingLeave != null ? $existingLeave->updated_at : '';
        if($db_record_timestamp == '' || $request_record_timestamp != $db_record_timestamp) {
            return response()->json('This record has been updated by another user. Kindly refresh the page before trying again.', 203);
        }

        if($existingLeave){
            $checkLeave = create_apply_leave_table::where('id',$request->id)->get();
        
            $from  = strtotime($request->from);
            $to = strtotime($request->to);
            $region_name = substr($request->empId,0,2);
            $datediff = $this->handleHolidaysAndWeekends($request->from, $request->to, $region_name);

            //check start end ,end date validation
            $start = new DateTime($request->from);
            $end = new DateTime($request->to);
            $diff = date_diff($start, $end);
            $finalDiff = $diff->format("%R%a");
            if($finalDiff >= 0){
                //Half day leave condition
                if($request->duration != 'Full Day' and $datediff == 1) {
                    $datediff = 0.5;
                }

                if($this->checkIfValidDateRange($request->empId, $request->from, $request->to, $id)) {
                    return response()->json('You already have one or more approved/applied leaves from the selected date range.', 203);
                }

                if($request->leaveid != 13) {
                    $currentLeaveBalance = $this->getEmployeeCurrentLeaveBalance($request->empId, $request->leaveid);
                    $pendingApprovalBalance = $this->getPendingLeaveDaysCountByEmpIdAndLeaveId($request->empId, $request->leaveid);

                    if((float)$datediff > (((float)$currentLeaveBalance[0]->balance + (float)$existingLeave->num_of_days) - (float)$pendingApprovalBalance)) {
                        return response()->json('You have insufficient leave balance.', 203);
                    }
                }

                try {
                    DB::beginTransaction();
                    $existingLeave->empid = $request->empId;
                    $existingLeave->leave_type = $request->leaveCat;
                    $existingLeave->leave_id = $request->leaveid;
                    $existingLeave->duration_type = $request->duration;
                    $existingLeave->reason = $request->reason;
                    $existingLeave->num_of_days = $datediff;
                    $existingLeave->start_date = $request->from;
                    $existingLeave->end_date = $request->to;
                    $existingLeave->status = $request->status;
                    $existingLeave-> save();

                    DB::commit();
                    
                } catch (\Exception $e) {   
                    DB::rollBack();
                    return response()->json('error', 500);
                }

                $request->merge(["type" => "Apply Leave", "subject" => "Leave Application", "fromAdd" => "notifications@theportal.scala-apac.org"]);
                try {
                    app('App\Http\Controllers\EmailController')->sendEmail($request);
                    } catch (\Exception $e) {
                        return $existingLeave;
                    }

                return $existingLeave;
            } else {
                return response()->json('Select valid dates', 203);
            }
        }
    }

    public function getDirectReporteeDetailsByManagerId(Request $request){

        $empAppliedData = [];

        $allDirectReportee = Employees::from('employees as em')
        ->join('users as ur', 'ur.employee_id', '=', 'em.empid')
        ->where('reporting_to', '=', $request->empid)
        ->get(['em.empid']);


        return $this->checkAppliedLeave($allDirectReportee);
    }

    public function checkAppliedLeave($empIds){
        
        $empPendingDetails = create_apply_leave_table::from('create_apply_leave_tables as ap')
        ->join('users as ur', 'ur.employee_id', '=', 'ap.empid')
        ->whereIn('ap.empid', $empIds)
        ->where('ap.status', 'pending')
        ->select('ap.id','ap.empid','ur.name','ap.leave_type','ap.leave_id','ap.duration_type','ap.reason','ap.status','ap.num_of_days','ap.start_date','ap.end_date','ap.medical_certificate', 'ap.updated_at')
        ->orderBy('ap.created_at')
        ->get();

        $empNonPendingDetails = create_apply_leave_table::from('create_apply_leave_tables as ap')
        ->join('users as ur', 'ur.employee_id', '=', 'ap.empid')
        ->whereIn('ap.empid', $empIds)
        ->where('ap.status', '!=', 'pending')
        ->select('ap.id','ap.empid','ur.name','ap.leave_type','ap.leave_id','ap.duration_type','ap.reason','ap.status','ap.num_of_days','ap.start_date','ap.end_date','ap.medical_certificate', 'ap.updated_at')
        ->orderBy('ap.updated_at', 'desc')
        ->get();

        $allData = $empPendingDetails->merge($empNonPendingDetails);
        return $allData;
    }

    public function uploadEmployeeMedicalAttachment(Request $request){ 

        $attachment = '';
        $fileTyepe = '';
        if($request->hasfile('file'))
        {
            $attachment = $request->File('file');
            $fileType = explode('.', $attachment->getClientOriginalName());
        }

        $base64 = 'data:image/'.$fileType[1].';base64,' . base64_encode(file_get_contents($attachment));
        $existingAppliedLeave = create_apply_leave_table::findOrFail($request->id);

        $existingAppliedLeave->medical_certificate = $base64;
        $existingAppliedLeave->save();

    }

    public function autoApproveLeave(Request $request, $existingLeave) {
        $empid = $existingLeave->empid;
        $leaveId = $existingLeave->leave_id;
        $daysOfLeave = $existingLeave->num_of_days;

        $currentLeaveBalance = $this->getEmployeeCurrentLeaveBalance($empid, $leaveId);
        $newBalance = (float)bcsub($currentLeaveBalance[0]->balance, $daysOfLeave, 2);
        $this->updateEmployeeNewBalance($empid, $leaveId, $newBalance); 
    }

    public function aprroveOrRejectEmployeeAppliedLeave(Request $request, $id){
        $empid = $request->leaveDetails['empid'];
        $leaveId = $request->leaveDetails['leaveId'];
        $daysOfLeave = $request->leaveDetails['totalDays'];
        
        $existingLeave = create_apply_leave_table::find($id);

        //Check if record was not modified between last read and current write
        $request_record_timestamp = Carbon::parse($request->leaveDetails['updatedAt'])->format('Y-m-d H:i:s');
        $db_record_timestamp = $existingLeave != null ? $existingLeave->updated_at : '';
        if($db_record_timestamp == '' || $request_record_timestamp != $db_record_timestamp) {
            return response()->json('This record has been updated by another user. Kindly refresh the page before trying again.', 203);
        }


        if($leaveId == '13'){
            try {
                DB::beginTransaction();
                $existingLeave->status = $request->leaveDetails['status'];
                if($request->leaveDetails['status'] != 'Cancelled') {
                    $existingLeave->comments = $request->leaveDetails['comments'];
                }
                $existingLeave->save();
                DB::commit();
            } catch (\Exception $e) {   
                DB::rollBack();
                return response()->json('error', 500);
            }   

            $request->merge(["type" => $request->leaveDetails['status']." Leave", "subject" => "Leave Application", "fromAdd" => "notifications@theportal.scala-apac.org", "autoApproved" => false, "type_of_leave" => $existingLeave->leave_type, "from" => $existingLeave->start_date, "to" => $existingLeave->end_date]);
            try {
                app('App\Http\Controllers\EmailController')->sendEmail($request);
            } catch (\Exception $e) {
                return response()->json('ok', 200);
            }

        }
        else if($request->leaveDetails['status'] == 'Approved'){
            try {
                    DB::beginTransaction();
                    $currentLeaveBalance = $this->getEmployeeCurrentLeaveBalance($empid, $leaveId);
                    $newBalance = (float)bcsub($currentLeaveBalance[0]->balance, $daysOfLeave, 2);
                    $this->updateEmployeeNewBalance($empid, $leaveId, $newBalance);
                    $existingLeave->status = $request->leaveDetails['status'];
                    $existingLeave->comments = $request->leaveDetails['comments'];

                    
                    $existingLeave->save();
                    DB::commit();
            } catch (\Exception $e) {   
                DB::rollBack();
                return response()->json('error', 500);
            }
            

            $request->merge(["type" => "Approved Leave", "subject" => "Leave Application", "fromAdd" => "notifications@theportal.scala-apac.org", "autoApproved" => false, "type_of_leave" => $existingLeave->leave_type, "from" => $existingLeave->start_date, "to" => $existingLeave->end_date]);
            try {
                app('App\Http\Controllers\EmailController')->sendEmail($request);
              } catch (\Exception $e) {
                  return response()->json('ok', 200);
              }

        }
        else if($request->leaveDetails['status'] == 'Cancelled'){
            try {
                DB::beginTransaction();
                $date = new Carbon();
                $today = $date->now()->format('Y-m-d');
                $existing_start_date = $existingLeave->start_date;

                $currentLeaveBalance = $this->getEmployeeCurrentLeaveBalance($empid, $leaveId);
                $newBalance = (float)bcadd($currentLeaveBalance[0]->balance, $daysOfLeave, 2);
                $this->updateEmployeeNewBalance($empid, $leaveId, $newBalance);
                $existingLeave->status = $request->leaveDetails['status'];

                if($today >= $existing_start_date){
                    Log::debug("Logic still needs to be written");
                    $new_start_date = $date->addDays(1)->format('Y-m-d');
                    $region_name = substr($existingLeave->empid,0,2);
                    $datediff = $this->handleHolidaysAndWeekends($existingLeave->start_date, $today, $region_name);
                    $leavedata = new create_apply_leave_table;
                    $leavedata->empid = $existingLeave->empid;
                    $leavedata->leave_type = $existingLeave->leave_type;
                    $leavedata->leave_id = $existingLeave->leave_id;
                    $leavedata->duration_type = $existingLeave->duration_type;
                    $leavedata->reason = $existingLeave->reason;
                    $leavedata->num_of_days = $datediff;
                    $leavedata->start_date = $existingLeave->start_date;
                    $leavedata->end_date = $today;
                    $leavedata->status = "Approved";

                    $leavedata-> save();
                    $currentLeaveBalance = $this->getEmployeeCurrentLeaveBalance($empid, $leaveId);
                    $newBalance = (float)bcsub($currentLeaveBalance[0]->balance, $datediff, 2);
                    $this->updateEmployeeNewBalance($empid, $leaveId, $newBalance);
                    $existingLeave->status = $request->leaveDetails['status'];
                    
                    
                }
                $existingLeave->save();
                DB::commit();
            } catch (\Exception $e) {   
                DB::rollBack();
                return response()->json('error', 500);
            }

            $request->merge(["type" => "Cancelled Leave", "subject" => "Leave Application", "fromAdd" => "notifications@theportal.scala-apac.org", "type_of_leave" => $existingLeave->leave_type, "from" => $existingLeave->start_date, "to" => $existingLeave->end_date]);
            try {
               app('App\Http\Controllers\EmailController')->sendEmail($request);
             } catch (\Exception $e) {
                 return response()->json('ok', 200);
             }
        }
        else if($request->leaveDetails['status'] == 'Rejected'){
            try {
                DB::beginTransaction();
                $existingLeave->status = $request->leaveDetails['status'];
                $existingLeave->comments = $request->leaveDetails['comments'];
                $existingLeave->save();
                DB::commit();
            } catch (\Exception $e) {   
                DB::rollBack();
                return response()->json('error', 500);
            }    

            $request->merge(["type" => "Rejected Leave", "subject" => "Leave Application", "fromAdd" => "notifications@theportal.scala-apac.org", "type_of_leave" => $existingLeave->leave_type, "from" => $existingLeave->start_date, "to" => $existingLeave->end_date]);
            try {
                app('App\Http\Controllers\EmailController')->sendEmail($request);
            } catch (\Exception $e) {
                return response()->json('ok', 200);
            }
        } 

        return response()->json('ok', 200);
    }

    public function getPendingLeaveDaysCountByEmpIdAndLeaveId($empid, $leaveId){
        
        $pendingLeaveBalance = create_apply_leave_table::from('create_apply_leave_tables as ap')
        ->where('ap.empid', '=', $empid)
        ->where('ap.leave_id', '=', $leaveId)
        ->where('ap.status', '=', 'pending')
        ->get(DB::raw('SUM(ap.num_of_days) as pendingDays'));

        return $pendingLeaveBalance[0]->pendingDays;
    }

    public function getEmployeeCurrentLeaveBalance($empid, $leaveId){
        
        $getLeaveBalance = Employee_leave_balance::from('employee_leave_balances as ba')
        ->where('ba.empid', '=', $empid)
        ->where('ba.leave_id', '=', $leaveId)
        ->get('ba.balance');

        return $getLeaveBalance;
    }

    public function updateEmployeeNewBalance($empid, $leaveId, $newBalance){
        
        $updateNewBalance = Employee_leave_balance::from('employee_leave_balances as ba')
        ->where('ba.empid', '=', $empid)
        ->where('ba.leave_id', '=', $leaveId)
        ->update([
            'balance' => $newBalance
         ]);

        return $updateNewBalance;
    }

    public function checkIfValidDateRange($empid, $from, $to, $existingId) {
        $condition_check1 = DB::table('create_apply_leave_tables as ca')
        ->where('ca.empid', '=', $empid)
        ->whereIn('ca.status', array('pending', 'Approved'))
        ->where('ca.id', '!=', $existingId)
        ->where(function ($query) use ($from, $to){
            $query->whereBetween('ca.start_date', [$from, $to])  
                  ->orWhereBetween('ca.end_date', [$from, $to]);
        })
        ->get();

        $condition_check2 = DB::table('create_apply_leave_tables as ca')
        ->where('ca.empid', '=', $empid)
        ->whereIn('ca.status', array('pending', 'Approved'))
        ->where('ca.id', '!=', $existingId)
        ->where('ca.start_date', '>=', $from)
        ->where('ca.end_date', '<=', $to)   
        ->get();

        $condition_check3 = DB::table('create_apply_leave_tables as ca')
        ->where('ca.empid', '=', $empid)
        ->whereIn('ca.status', array('pending', 'Approved'))
        ->where('ca.id', '!=', $existingId)
        ->where('ca.start_date', '<=', $from)
        ->where('ca.end_date', '>=', $to)   
        ->get();

        if(count($condition_check1) || count($condition_check2) || count($condition_check3))
            return true;

        return false ;   
    }
}
