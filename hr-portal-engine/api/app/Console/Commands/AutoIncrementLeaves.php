<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Employee_leave_balance;
use App\Models\Employees;
use App\Models\LeaveEntitlementTable;
use App\Models\LeaveAccrualRules;
use App\Http\Controllers\ApplicationJobController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\EmailController;
use DB;

class AutoIncrementLeaves extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        // $this->handle();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {   
        $name = 'APAC_LEAVE_ACCRUAL';
        $description = 'To credit leaves to employees across APAC';

        $appJobControllerObject = new ApplicationJobController();
        $emailControllerObject = new EmailController();

        $successJobFlag = $appJobControllerObject->checkIfAnyJobPresentByName($name);
        
        if(! $successJobFlag) {
            
            $leaveAccrualJob = $appJobControllerObject->createJob($name, $description);

            //Capturing leave balances before accrual
            $request = new Request();
            $request->fType = 'xlsx';
            $request->rType = 'accrual';
            $request->eType = 'before';
            $reportControllerObject = new ReportController();
            $attachment_before = $reportControllerObject->generateReport($request); 

            try {
                DB::beginTransaction();
                $rules = LeaveAccrualRules::from('leave_accrual_rules as rl')
                ->join('leave_names as ln', 'ln.id', '=', 'rl.leave_type')
                ->get(['ln.id', 'ln.name', 'rl.region', 'rl.accrual', 'rl.leave_type', 'rl.carry_forword', 'rl.max_limit']);;
                $empData = Employees::from('employees as em')
                ->join('users as ur', 'ur.employee_id', '=', 'em.empid')
                ->where('em.status', '=', 'active')
                ->where('em.employement_type', '=', 'Full Time Employee')
                ->get(['em.id', 'em.empid', 'em.location']);

                foreach($empData as $emp) {
                    $empLeaveBalance = Employee_leave_balance::from('employee_leave_balances as em')
                    ->join('leave_names as na', 'na.id', '=', 'em.leave_id')
                    ->where('em.empid', '=', $emp['empid'] )
                    ->get(['na.id', 'na.name', 'em.balance','em.empid']);
                    $empEntitlementData = LeaveEntitlementTable::where('empid', '=', $emp['empid'] )
                    ->get();
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
                                $addLeaveBalence = array();
                                if($isAnyRule) {
                                    if( $ruleVal['max_limit'] == 'NA') {
                                        $addLeaves = floatval($levBal['balance']) + floatval($value);
                                        $addLeaveBalence['balance'] = $addLeaves;
                                    } else if( is_numeric(trim($ruleVal['max_limit']))) {
                                        $addLeaves = floatval($levBal['balance']) + floatval($value);
                                        $LeavesLimit = floatval(trim($ruleVal['max_limit']));
                                        $finalVal = ($addLeaves > $LeavesLimit ? $LeavesLimit : $addLeaves);
                                        $addLeaveBalence['balance'] = $finalVal;
                                    } else if( $ruleVal['max_limit'] == '24 months') {
                                        $oldBal = floatval($levBal['balance']);
                                        $entVal = floatval($value) * 2;
                                        $carry_forword_val = ($oldBal > $entVal ? $entVal : $oldBal);
                                        $addLeaveBalence['balance'] = $carry_forword_val + floatval($value);
                                    }
                                } else {
                                    $addLeaveBalence['balance'] = $value;
                                }
                                $updateBal = Employee_leave_balance::from('employee_leave_balances as em')
                                ->where('em.empid', '=', $emp['empid'])
                                ->where('em.leave_id', '=', $levBal['id'])
                                ->update($addLeaveBalence);
                            }
                        }
                    }
                }

                $leaveAccrualJob->job_status = 'Success';
                $appJobControllerObject->updateJob($leaveAccrualJob);  
                
                DB::commit();

                //Capturing leave balances after accrual
                $request->fType = 'xlsx';
                $request->rType = 'accrual';
                $request->eType = 'after';
                $attachment_after = $reportControllerObject->generateReport($request); 
                
                $request->merge(["type" => "Accrual Success", "fType" => "xlsx"]);
                $emailControllerObject->sendEmail($request);
            } catch (\Exception $e) {   
                DB::rollBack();
                $leaveAccrualJob->exception = $e->getMessage();
                $leaveAccrualJob->job_status = 'Failed';
                $appJobControllerObject->updateJob($leaveAccrualJob);
                $request->merge(["type" => "Accrual Failed"]);
                $emailControllerObject->sendEmail($request);
            }
        }
    }

}
