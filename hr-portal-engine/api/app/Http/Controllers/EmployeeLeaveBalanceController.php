<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Employee_leave_balance;
use App\Models\Employees;
use App\Models\LeaveEntitlementTable;
use App\Models\LeaveAccrualRules;
use App\Models\create_apply_leave_table;
use DB;
use Carbon\Carbon;

class EmployeeLeaveBalanceController extends Controller
{
    const EMPLOYEE_LEAVE_EXPIRY_DATA = [self::class, 'getLeaveExpiryData'];
    const UPDATE_LEAVE_BALANCE = [self::class, 'updateLeaveBal'];

    public function getLeaveExpiryData($empId) {
        
        $rules = LeaveAccrualRules::from('leave_accrual_rules as rl')
        ->join('leave_names as ln', 'ln.id', '=', 'rl.leave_type')
        ->get(['ln.id', 'ln.name', 'rl.region', 'rl.accrual', 'rl.leave_type', 'rl.carry_forword', 'rl.max_limit']);;
        $empData = Employees::from('employees as em')
        ->join('users as ur', 'ur.employee_id', '=', 'em.empid')
        ->where('em.status', '=', 'active')
        ->where('em.employement_type', '=', 'Full Time Employee')
        ->where('em.empid', '=', $empId)
        ->get(['em.id', 'em.empid', 'em.location', 'start_Date']);

        $leavesExpiring = array('empid' => $empId);
        foreach($empData as $emp) {

            // $regionsForVerifyOnStartData = array('Singapore', 'Japan');
            $regionsForVerifyOnStartData = array();
            if(in_array(trim($emp['location']), $regionsForVerifyOnStartData)) {
                $empStartDate = Carbon::parse($emp['start_Date']); //->format('d-m-Y');
                $empStartDate = $empStartDate->month > 6 ? $empStartDate->year(now()->format('Y')) : $empStartDate->year(now()->addYear()->format('Y'));
                $leaveExpiryDate = $empStartDate->format('d-m-Y');
            } else {
                $leaveExpiryDate = '31-12-' . Carbon::now()->format('Y');
            }

            $notifyFrom = Carbon::create($leaveExpiryDate)->subMonths(6);
            $today = Carbon::now();
            
            if($today->lt(Carbon::parse($leaveExpiryDate)) && $today->gt($notifyFrom)) {

                $empLeaveBalance = Employee_leave_balance::from('employee_leave_balances as em')
                ->join('leave_names as na', 'na.id', '=', 'em.leave_id')
                ->where('em.empid', '=', $emp['empid'])
                ->get(['na.id', 'na.name', 'em.balance','em.empid']);

                $empEntitlementData = LeaveEntitlementTable::where('empid', '=', $emp['empid'] )
                ->get(['id', 'empid', 'annual_leave', 'sick_leave', 'optional_leave', 'compensatory_leave', 'marriage_leave', 'bereavement_leave', 'maternity_leave', 'paternity_leave', 'childcare_leave', 'national_service_leave', 'hospitalization_leave', 'carers_leave']);
                
                // $appliedLeaves = create_apply_leave_table::where('empid', '=', $emp['empid'])
                // ->where(function($query)
                // {
                //     $today = Carbon::now()->addYears(1);
                //     $newYear = Carbon::create($today->year, 1, 1, 0);
                //     $query->whereDate('start_date', '>', $newYear)
                //     ->orWhereDate('end_date', '>', $newYear);
                // })
                // ->get(['id', 'leave_type', 'leave_id', 'duration_type', 'status', 'num_of_days', 'start_date', 'end_date']);
    
                foreach($empLeaveBalance as $levBal) {
                    $isAnyRule = false;
                    $ruleVal = array();
                    foreach($rules as $rule) {
                        if ($emp['location'] == $rule['region'] && $levBal['id'] == $rule['id'] && $rule['carry_forword'] == 'yes') {
                            $isAnyRule = true;
                            $ruleVal = $rule;
                            break;
                        }
                    }
                    $tempEntData = json_decode($empEntitlementData[0], true);
                    foreach($tempEntData as $key=>$value) {
                        $splitKey = explode("_", $key);
                        $splitLeaveName = explode(" ", $levBal['name']);
                        if($value != null && strtolower($splitLeaveName[0]) == strtolower($splitKey[0])) {
                            $addLeaveBalance = array('leave_name' => $levBal['name'], 'leave_id' => $levBal['id'], 'balance' => $levBal['balance'], 'expiry_date' => $leaveExpiryDate);
                            if($isAnyRule) {
    
                                // foreach($appliedLeaves as $upcomingLeave) {
    
                                // }
    
                                if($ruleVal['carry_forword'] == 'yes' && is_numeric(trim($ruleVal['max_limit']))) {
                                    $addLeaves = floatval($levBal['balance']) + floatval($value);
                                    $LeavesLimit = floatval(trim($ruleVal['max_limit']));
                                    $finalVal = ($addLeaves > $LeavesLimit ? $addLeaves - $LeavesLimit : 0);
                                    $addLeaveBalance['no_of_leaves_expiring'] = $finalVal;
                                } else if($ruleVal['carry_forword'] == 'yes' && $ruleVal['max_limit'] == '24 months') {
                                    $oldBal = floatval($levBal['balance']);
                                    $entVal = floatval($value) * 2;
                                    $finalVal = ($oldBal > $entVal ? $oldBal-$entVal : 0);
                                    $addLeaveBalance['no_of_leaves_expiring'] = $finalVal;
                                }
                            } else {
                                $matchConditions = array('', '0', 'NA');
                                $lvBalance = in_array(trim($levBal['balance']), $matchConditions) ? 0 : floatval(trim($levBal['balance']));
                                $addLeaveBalance['no_of_leaves_expiring'] = $levBal['balance'];
                            }
                            try {
                                if($addLeaveBalance['balance'] != '' && $addLeaveBalance['balance'] != 0 && $addLeaveBalance['no_of_leaves_expiring'] != 0) {
                                    array_push($leavesExpiring, $addLeaveBalance);
                                }
                            } catch (\Throwable $th) {
                            }
                        }
                    }
                }
            }
        }
        Log::debug($leavesExpiring);

        return response()->json(
            $leavesExpiring
        , 200);
    }


    public function updateLeaveBal(Request $request){
        try {
            DB::beginTransaction();
            foreach($request->data as $eachLeaveData) {
                $empId = $eachLeaveData['empid'];
                $leaveName = trim(str_replace('Balance', '', $eachLeaveData['name']));
                $leaveId = $eachLeaveData['leave_name_id'];
                $leaveBalance = $eachLeaveData['balance'];
                if($leaveId != 13) {
                    if($empId == '') {
                        return response()->json('Please select an employee', 203);
                    } else if($leaveBalance != '' and $leaveBalance != null and $leaveBalance != 'NA' and !(is_numeric($leaveBalance))) {
                        return response()->json('Invalid value entered for '.$leaveName, 203);
                    }
                    $existingLeaveData = Employee_leave_balance::from('employee_leave_balances as em')
                        ->where('em.empid',  $empId)
                        ->where('em.leave_id', $leaveId)
                        ->update(['em.balance' => $leaveBalance]); 
                } 
            }
            
            DB::commit();
        } catch (\Exception $e) { 
            DB::rollBack();
            return response()->json('error', 500);
        }

        return response()->json('ok', 200);
    }
}
