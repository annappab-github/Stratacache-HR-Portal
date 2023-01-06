<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;
use App\Mail\EmailQueue;
use App\Models\Employees;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $requestDetails;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->requestDetails = $details;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {   
        $requestType = $this->requestDetails['type'];

        if($requestType == "Reset Password Notification") {
            $this->requestDetails['url'] = $this->requestDetails['message'];
            Mail::to($this->requestDetails['to'])
                ->send(new EmailQueue($this->requestDetails));
        } else if($requestType == "New Employee") {
            $this->requestDetails['url'] = env('THE_PORTAL_URL');
            $this->requestDetails['name'] = $this->requestDetails['name'];
            $this->requestDetails['username'] = $this->requestDetails['email'];
            $this->requestDetails['password'] = $this->requestDetails['password'];
            Mail::to($this->requestDetails['email'])
                ->send(new EmailQueue($this->requestDetails));
        } else if($requestType == "New Reportee") {
            if($this->requestDetails['reporting_to'] != ''){
                $managerData = Employees::from('employees as em')
                ->join('users as ur', 'ur.employee_id', '=', 'em.empid')
                ->where('empid', '=', $this->requestDetails['reporting_to'])
                ->get(['em.id', 'em.empid', 'ur.name', 'ur.email']);

                if(count($managerData) > 0) {
                    Mail::to($managerData[0]['email'])
                        ->send(new EmailQueue($this->requestDetails));
                }
            }
        } else if($requestType == "Apply Leave") {
            $checkEmployeeReportingManager = Employees::from('employees as em')
            ->join('users as ur', 'ur.employee_id', '=', 'em.empid')
            ->where('em.empid', '=', $this->requestDetails['empId'])
            ->get(['em.reporting_to','ur.email','ur.name']);

            $empEmail = $checkEmployeeReportingManager[0]->email;
            $empName = $checkEmployeeReportingManager[0]->name;
            $fromDate = $this->requestDetails['from'];
            $toDate = $this->requestDetails['to'];
            $typeOfLeave = $this->requestDetails['leaveCat'];

            $managerId = $checkEmployeeReportingManager[0]->reporting_to;
            $managerData = Employees::from('employees as em')
            ->join('users as ur', 'ur.employee_id', '=', 'em.empid')
            ->where('empid', '=', $managerId)
            ->get(['em.id', 'em.empid', 'ur.name', 'ur.email']);

            if(count($managerData) > 0) {

                $employeeMailId = $empEmail;
                $this->requestDetails['manager_name'] = $managerData[0]['name'];
                $this->requestDetails['employee_name'] = $empName;
                $this->requestDetails['from_date'] = $fromDate;
                $this->requestDetails['to_date'] = $toDate;
                $this->requestDetails['type_of_leave'] = $typeOfLeave;
                $this->requestDetails['url'] = env('THE_PORTAL_URL');
                if(trim($this->requestDetails['reason']) != '') {
                    $this->requestDetails['reason'] = ' with comments "'.$this->requestDetails['reason'].'"';
                } 

                Mail::to($managerData[0]['email'])
                    ->cc(env('LEAVE_CC'))
                    ->send(new EmailQueue($this->requestDetails));
            }
        } else if ($requestType == "Approved Leave") {
            $empId = $this->requestDetails['leaveDetails']['empid'];
            $leaveStatus = $this->requestDetails['leaveDetails']['status'];
            $totalDays = $this->requestDetails['leaveDetails']['totalDays'];
            $typeOfLeave = $this->requestDetails['leaveDetails']['leaveType'];
            $comments = $this->requestDetails['leaveDetails']['comments'];
            
            $employeeDetails = Employees::from('employees as em')
            ->join('users as ur', 'ur.employee_id', '=', 'em.empid')
            ->where('em.empid', '=', $empId)
            ->get(['ur.name','ur.email','em.reporting_to']);

            if(count($employeeDetails) > 0) {

                $this->requestDetails['employee_name'] = $employeeDetails[0]->name;
                $this->requestDetails['leave_status'] = $leaveStatus;
                $this->requestDetails['total_days'] = $totalDays;
                $this->requestDetails['for'] = 'requestor';
                $this->requestDetails['type_of_leave'] = $typeOfLeave;
                $this->requestDetails['comments'] = $comments;
                if(trim($comments) != '') {
                    $this->requestDetails['comments'] = ' with comments "'.$comments.'"';
                } 

                Mail::to($employeeDetails[0]->email)
                    ->cc(env('LEAVE_CC'))
                    ->send(new EmailQueue($this->requestDetails));
            }
        } else if ($requestType == "Rejected Leave") {
            $empId = $this->requestDetails['leaveDetails']['empid'];
            $leaveStatus = $this->requestDetails['leaveDetails']['status'];
            $totalDays = $this->requestDetails['leaveDetails']['totalDays'];
            $typeOfLeave = $this->requestDetails['leaveDetails']['leaveType'];
            $comments = $this->requestDetails['leaveDetails']['comments'];

            $employeeDetails = Employees::from('employees as em')
            ->join('users as ur', 'ur.employee_id', '=', 'em.empid')
            ->where('em.empid', '=', $empId)
            ->get(['ur.name','ur.email','em.reporting_to']);

            if(count($employeeDetails) > 0) {

                $this->requestDetails['employee_name'] = $employeeDetails[0]->name;
                $this->requestDetails['leave_status'] = $leaveStatus;
                $this->requestDetails['total_days'] = $totalDays;
                $this->requestDetails['for'] = 'requestor';
                $this->requestDetails['type_of_leave'] = $typeOfLeave;
                $this->requestDetails['comments'] = $comments;

                Mail::to($employeeDetails[0]->email)
                    ->cc(env('LEAVE_CC'))
                    ->send(new EmailQueue($this->requestDetails));
            }
        } else if ($requestType == "Cancelled Leave") {
            $empId = $this->requestDetails['leaveDetails']['empid'];
            $leaveStatus = $this->requestDetails['leaveDetails']['status'];
            $totalDays = $this->requestDetails['leaveDetails']['totalDays'];

            $employeeDetails = Employees::from('employees as em')
            ->join('users as ur', 'ur.employee_id', '=', 'em.empid')
            ->where('em.empid', '=', $empId)
            ->get(['ur.name','ur.email','em.reporting_to']);

            $reprotingManagerId = $employeeDetails[0]->reporting_to;
            $managerDetails = Employees::from('employees as em')
            ->join('users as ur', 'ur.employee_id', '=', 'em.empid')
            ->where('em.empid', '=', $reprotingManagerId)
            ->get(['ur.email','ur.name']);

            if(count($employeeDetails) > 0) {

                $this->requestDetails['employee_name'] = $employeeDetails[0]->name;
                $this->requestDetails['leave_status'] = $leaveStatus;
                $this->requestDetails['total_days'] = $totalDays;
                $this->requestDetails['for'] = 'requestor';

                Mail::to($employeeDetails[0]->email)
                    ->cc(env('LEAVE_CC'))
                    ->send(new EmailQueue($this->requestDetails));
            }
            if(count($managerDetails) > 0) {
                $this->requestDetails['manager_name'] = $managerDetails[0]->name;
                $this->requestDetails['for'] = 'line_manager';
                Mail::to($managerDetails[0]->email)
                    ->cc(env('LEAVE_CC'))
                    ->send(new EmailQueue($this->requestDetails));
            }
        } else if ($requestType == "Accrual Success") {
            $this->requestDetails['fromAdd'] = 'notifications@theportal.scala-apac.org';
            $this->requestDetails['accrual_year'] = Carbon::now()->year;
            Mail::to(env('ACCRUAL_SUCCESS_TO'))
                    ->send(new EmailQueue($this->requestDetails));
        } else if ($requestType == "Accrual Failed") {
            $this->requestDetails['fromAdd'] = 'notifications@theportal.scala-apac.org';
            $this->requestDetails['accrual_year'] = '2023';
            $this->requestDetails['exception'] = '$details not defined';
            Mail::to(env('ACCRUAL_FAILURE_TO'))
                    ->send(new EmailQueue($this->requestDetails));
        } else {
            $this->requestDetails['url'] = $this->requestDetails['message'];
            if( $this->requestDetails['to'] == 'MD APAC') {
                Mail::to(env('WHITLE_BLOWER_TO'))
                    ->send(new EmailQueue($this->requestDetails));
            }else {
                Mail::to($this->requestDetails['to'])
                    ->send(new EmailQueue($this->requestDetails));
            }
        }
    }
}
