<?php

namespace App\Http\Controllers;

use App\Models\Employees;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class EmployeesController extends Controller
{
    const GET_ALL_EMPLOYEES = [self::class, 'getAllEmployees'];
    const GET_EMPLOYEE_INFO = [self::class, 'getEmployeeInfo'];
    const GET_EMPLOYEE_INFO_BY_EMAIL = [self::class, 'getEmployeeInfoByEmail'];
    const UPDATE_EMPLOYEE = [self::class, 'updateEmployee'];
    const GET_EMPLOYEES_DATA_FOR_REPORTING = [self::class, 'getEmployeesForReporting'];

    public function getAllEmployees(Request $request) {
        $userRole = $request->role;
        // if (strtolower($userRole) == 'employee') {
            $employees = Employees::from('employees as em')
            ->join('users as ur', 'ur.employee_id', '=', 'em.empid')
            ->where('status',  '=', 'active')
            ->where('display_to_employees', '=', 1)
            ->orderBy('ur.name')
            ->get(['em.id', 'em.empid', 'ur.name', 'ur.email', 'em.location', 'em.city', 'em.position', 'em.department', 'em.land_ext', 'em.mobile_number', 'em.reporting_to', 'em.profile_pic', 'em.status', 'display_to_employees']);
        // } else {
        //     $employees = Employees::from('employees as em')
        //     ->join('users as ur', 'ur.employee_id', '=', 'em.empid')
        //     ->orderBy('ur.name')
        //     ->get(['em.id', 'em.empid', 'ur.name', 'ur.email', 'em.location', 'em.position', 'em.department', 'em.land_ext', 'em.mobile_number', 'em.reporting_to', 'em.profile_pic', 'em.status']);
        // }        

        $formattedArray = [];
        foreach($employees as $eachData) {
            $eachData['employeeShortName'] = $this->getEmployeeShortName(trim($eachData['name']));
            $eachData['imageBackgroundColour'] = $this->getImageBackgroundColour(trim($eachData['name']));
            $eachData['reporting_manager_name'] = $this-> getReportingManagerName($eachData['reporting_to'], $employees);
            $eachData['direct_reportees'] = $this-> getDirectReporteesCount($eachData['empid'], $employees);
            array_push($formattedArray, $eachData);
        }
        return response()->json([
            'employees' => $formattedArray
        ], 200);
        
    }

    public function getDirectReporteeDetailsByEmployeeId($empId) {
        // Log::debug($empId);
        $allDirectReportee = Employees::from('employees as em')
        ->join('users as ur', 'ur.employee_id', '=', 'em.empid')
        ->where('status', '=', 'active')
        ->where('reporting_to', '=', $empId)
        ->get(['em.id', 'em.empid', 'ur.name', 'ur.email', 'em.profile_pic']);

        foreach($allDirectReportee as $eachReportee) {
            $eachReportee['employeeShortName'] = $this->getEmployeeShortName(trim($eachReportee['name']));
            $eachReportee['imageBackgroundColour'] = $this->getImageBackgroundColour(trim($eachReportee['name']));
        }

        return $allDirectReportee;
    }

    public function getEmployeeInfo($empid) {
        $existingEmployee = Employees::from('employees as em')
        ->join('users as ur', 'ur.employee_id', '=', 'em.empid')
        ->where('em.empid', '=', $empid)
        ->get(['em.id as emid', 'ur.id as urid', 'em.empid', 'ur.name', 'ur.email','em.gender','em.employement_type', 'ur.force_change_password', 'em.start_Date', 'em.employee_grade', 'em.location', 'em.city', 'em.employement_type', 'em.position', 'em.department', 'em.land_ext', 'em.mobile_number', 'em.reporting_to', 'em.birth_dates', 'em.probation_period', 'em.confirmation_date', 'em.remarks', 'em.new_confirmationdate', 'em.status', 'em.address', 'em.profile_pic', 'em.terminationdate', 'em.resignation', 'em.last_workingdate', 'display_to_employees']);

        $users = User::where('employee_id', '=', $empid)->get();
        $users->transform(function($user){
            $user->role = $user->getRoleNames()->first();
            return $user;
        });
        $existingEmployee[0]->role = $users[0]->role;

        $formattedArray = [];

        foreach($existingEmployee as $eachData) {
            $eachData['employeeShortName'] = $this->getEmployeeShortName(trim($eachData['name']));
            $eachData['imageBackgroundColour'] = $this->getImageBackgroundColour(trim($eachData['name']));
            array_push($formattedArray, $eachData);
        }
        return response()->json(
            $formattedArray
        , 200);
    }

    public function getEmployeeInfoByEmail($email) {
        $existingEmployee = Employees::from('employees as em')
        ->join('users as ur', 'ur.employee_id', '=', 'em.empid')
        ->where('ur.email', '=', $email)
        ->get(['em.id as emid', 'ur.id as urid', 'em.empid', 'ur.name', 'ur.email', 'ur.force_change_password', 'em.start_Date', 'em.employee_grade', 'em.location', 'em.city', 'em.employement_type', 'em.position', 'em.department', 'em.land_ext', 'em.mobile_number', 'em.reporting_to', 'em.birth_dates', 'em.probation_period', 'em.confirmation_date', 'em.remarks', 'em.new_confirmationdate', 'em.status', 'em.address', 'em.profile_pic', 'em.terminationdate', 'em.resignation', 'em.last_workingdate', 'display_to_employees']);
        $users = User::where('email', '=', $email)->get();
        $users->transform(function($user){
            $user->role = $user->getRoleNames()->first();
            return $user;
        });
        $existingEmployee[0]->role = $users[0]->role;

        $formattedArray = [];

        foreach($existingEmployee as $eachData) {
            // Log::debug($eachData);
            $eachData['employeeShortName'] = $this->getEmployeeShortName(trim($eachData['name']));
            $eachData['imageBackgroundColour'] = $this->getImageBackgroundColour(trim($eachData['name']));
            $eachData['reportee_count'] = count($this->getDirectReporteeDetailsByEmployeeId($eachData['empid']));
            array_push($formattedArray, $eachData);
        }
        return response()->json(
            $formattedArray
        , 200);
    }

    public function updateEmployee(Request $request) {
        $employee = Employees::findOrFail($request->id);
        $employee->address = trim($request->address);
        $employee->birth_dates = $request->birth_dates;
        $employee->confirmation_date = $request->confirmation_date;
        $employee->department = trim($request->department);
        $employee->employee_grade = trim($request->employee_grade);
        $employee->land_ext = trim($request->land_ext);
        $employee->location = trim($request->location);
        $employee->mobile_number = trim($request->mobile_number);
        $employee->new_confirmationdate = $request->new_confirmationdate;
        $employee->position = trim($request->position);
        $employee->probation_period = trim($request->probation_period);
        $employee->remarks = trim($request->remarks);
        $employee->reporting_to = trim($request->reporting_to);
        $employee->start_Date = $request->start_Date;
        $employee->status = $request->status;
        $employee->save();

        return response()->json([
            'response' => 'success',
        ],200);
    }

    public function forgotPassword(Request $request) {
        $existingEmployee = Employees::from('employees as em')
        ->join('users as ur', 'ur.employee_id', '=', 'em.empid')
        ->where('ur.email', trim($request->to))
        ->where('em.status', 'active')
        ->get();

        if(count($existingEmployee) == 0) {
            return response()->json('No active user exists with email '.trim($request->to), 203);
        }

        app('App\Http\Controllers\EmailController')->sendEmail($request);

        return response()->json('ok', 200);
    }

    public function getEmployeesForReporting() {
        // ->where('em.empid', '!=', $empid)
        $existingEmployee = Employees::from('employees as em')
        ->join('users as ur', 'ur.employee_id', '=', 'em.empid')
        ->orderBy('ur.name')
        ->get(['em.id', 'em.empid', 'ur.name', 'em.position']);
        return response()->json(
            $existingEmployee
        , 200);
    }

    public function getReportingManagerName($managerEmpId, $employees) {
        $manager = "";
        foreach($employees as $eachData) {
            if($eachData['empid'] == $managerEmpId) {
                $manager = $eachData['name'];
                break;
            }
        }
        return $manager;
    }

    public function getDirectReporteesCount($empId, $employees) {
        $count = 0;
        foreach($employees as $eachData) {
            if($eachData['reporting_to'] == $empId and $eachData['status'] == 'active') {
                $count = $count + 1;
            }
        }
        return $count;
    }

    public function getEmployeeShortName($name) {
        $splitName = explode(' ',$name);
        $firstName = array_shift($splitName);
        $lastName = end($splitName);

        return substr($firstName, 0, 1).substr($lastName, 0, 1);
    }

    public function getImageBackgroundColour($name) {
        $wordCount = strlen($name);

        switch($wordCount % 5) {
            case(0):
                return "#ff7440";
                break;
                
            case(1):
                return "#32cd32";
                break;

            case(2):
                return "#cf9fff";  
                break;
                
            case(3):
                return "#ff7f7f";   
                break;
                
            case(4):
                return "#464c5e";
                break;
        }

        return "#122e55";
    }

}
