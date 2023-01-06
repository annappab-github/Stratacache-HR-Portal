<?php

namespace App\Http\Controllers;

use App\Models\Gate;
use App\Exports\CsvExcelExport;
use App\Exports\ALSLBalanceExcelExport;
use App\Exports\LeaveBalanceExcelExport;
use App\Exports\AccrualExcelExport;
use PDF;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromView;
use OwenIt\Auditing\Models\Audit;
use Excel;
use DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Employees;
use App\Models\Employee_leave_balance;
use App\Models\Leave_name;
use App\Models\holidaylist;
use App\Models\Workingsaturdays;
use App\Models\create_apply_leave_table;

class ReportController extends Controller
{
  private $recordCount;

  public function generateReport(Request $request) {
    $this->recordCount = 0;
    switch($request->fType) {
      case('pdf'):
        return $this->generatePdfReport($request);
        
      case('xlsx'):
      case('csv'):
        return $this->generateCsvExcelReport($request);   
    }
  }

  public function generatePdfReport(Request $request) {  
    switch($request->rType) {
      case('gate'):
        $data = $this->getGateReportData($request);
        if($this->recordCount == 0) {
          return [];
        }                 
        $pdf = PDF::loadView('gates', $data);
		break;
        
      case('audit'):
        $data = $this->getAuditReportData($request);
        if($this->recordCount == 0) {
          return [];
        }                 
        $pdf = PDF::loadView('audits', $data)->setPaper('a3', 'landscape');
		break;

        case('employee'):
          $data = $this->getEmployeeReportData($request);
          if($this->recordCount == 0) {
            return [];
          }              
          $pdf = PDF::loadView('employees', $data)->setPaper('a3', 'landscape');
      break;
    } 
    return $pdf->output();            
    //return $pdf->download('Gates_'.date('Y-m-d h:i:sa').'.pdf');
  } 

  public function generateCsvExcelReport(Request $request) { 
    $fileFormat = $request->fType;
    switch($request->rType) {
      case('leaveBalanceALSL'):
        $exporter = Excel::download(new LeaveBalanceExcelExport, 'report.'.$fileFormat);
        break;

      case('leaveBalanceAll'):
        $exporter = Excel::download(new AccrualExcelExport, 'report.'.$fileFormat);
        break;  
    
      case('accrual'):
        $fileName = "Accrual_".Carbon::now()->year.'.'.$fileFormat;
        if($request->eType == "before"){
          $fileName = "Before_".$fileName;
        } else {
          $fileName = "After_".$fileName;
        }
        Excel::store(new AccrualExcelExport, $fileName, 'local');
        $exporter = Excel::download(new AccrualExcelExport, 'report.'.$fileFormat);
        break;  

      case('audit'): 
      case('employee'):   
        $exporter = Excel::download(new CsvExcelExport, 'report.'.$fileFormat);
        break; 
    }
    
    // Log::debug($this->recordCount);  

    error_log($exporter);

    return $exporter;
  }

  public function getGateReportData(Request $request) {
    //date_default_timezone_set("Asia/Kolkata");
    $this->recordCount = 0;
    $name = $request->rName;
    $user = $request->uName;
    $filter = $request->filter;
    $fromDate = $request->fDate.' 00:00:00';
    $toDate = $request->tDate.' 23:59:59';

    $headers = ['Name', 'Status', 'Created At', 'Modified At'];
    if($filter == 'all'){
      $data = Gate::select('display_name', 'is_active', 'created_at', 'updated_at')->whereBetween('created_at', [$fromDate, $toDate])->get();
    } else {
      $data = Gate::select('display_name', 'is_active', 'created_at', 'updated_at')->where('is_active', $filter)->whereBetween('created_at', [$fromDate, $toDate])->get();
    }
    
    // Log::debug(count($data));
    $this->recordCount = count($data);

    $fullData = [
        'reportTitle' => 'Report('.date('Y-m-d h:i:s').')',
        'reportName' => $name,
        'generatedBy' => $user,
        'fromDate' => explode(" ", $fromDate)[0],
        'toDate' => explode(" ", $toDate)[0],
        'reportHeader' => $headers,
        'reportData' => $data
    ];

    return $fullData;
  }

  public function getAuditReportData(Request $request) {
    //date_default_timezone_set("Asia/Kolkata");
    $this->recordCount = 0;
    $name = $request->rName;
    $user = $request->uName;
    $filter = $request->filter;
    $fromDate = $request->fDate.' 00:00:00';
    $toDate = $request->tDate.' 23:59:59';

    $headers = ['User', 'Audit Type', 'Audit Id', 'Old Values', 'New Values', 'Event', 'Created At', 'Modified At', 'IP Address'];
    //$data = Audit::select('auditable_type', 'event', 'ip_address', 'created_at', 'updated_at')->whereBetween('created_at', [$fromDate, $toDate])->get();
    $data = DB::table('audits')->join('users', 'audits.user_id', '=', 'users.id')->select('users.name', 'audits.auditable_type', 'audits.auditable_id', 'audits.old_values', 'audits.new_values', 'audits.event', 'audits.created_at', 'audits.updated_at', 'audits.ip_address')->whereBetween('audits.created_at', [$fromDate, $toDate])->get();
    
    // Log::debug(">>>>>>>>>>>>>>>>>>>> Record Count >>>>>>>>>>>>>>>>>>>".count($data));
    $this->recordCount = count($data);

    $fullData = [
        'reportTitle' => 'Report('.date('Y-m-d h:i:s').')',
        'reportName' => $name,
        'generatedBy' => $user,
        'fromDate' => explode(" ", $fromDate)[0],
        'toDate' => explode(" ", $toDate)[0],
        'reportHeader' => $headers,
        'reportData' => $data
    ];

    return $fullData;
  }

  public function getEmployeeReportData(Request $request){
    error_log($request);
    $this->recordCount = 0;

    $name = $request->rName;
    $cities = array();
    $countries = array();
    foreach($request->country_city as $countryCity) {
      $splitval = explode("-", $countryCity);
      array_push($countries, count($splitval) == 1 ? $splitval[0] : $splitval[1]);
      array_push($cities, $splitval[0]);
    }

    $department = $request->department;
    $status = $request->status;
    $empType = $request->empType;
    $fromDate = $request->fDate.' 00:00:00';
    $toDate = $request->tDate.' 23:59:59';

    $headers = ['Employee ID','Start Date','Employee Grade','Employee Name','location','City','Position','Department','Land Ext','Mobile No','Email','Reporting To',
    'Birthdate','Probation Period','Confirmation Date','Remarks','New Confirmation Date','Employee Type','status'];


      $data = Employees::from('employees as em')
      ->join('users as ur', 'ur.employee_id', '=', 'em.empid')
      ->whereIn('city', array_map('trim', $cities))
      ->whereIn('location', array_map('trim', $countries))
      ->whereIn('status', $status)
      ->whereIn('department', $department)
      ->whereIn('employement_type',$empType)
      ->whereBetween('start_Date', [$fromDate, $toDate])
      ->get(['em.empid','em.start_Date','em.employee_grade','ur.name','em.location','em.position','em.department','em.land_ext','em.mobile_number','ur.email','em.reporting_to',
      'em.birth_dates','em.probation_period','em.confirmation_date','em.remarks','em.new_confirmationdate','em.city','em.employement_type','em.status']);


    $this->recordCount = count($data);


    $fullData = [
      'reportTitle' => 'Report('.date('Y-m-d h:i:s').')',
      'reportName' => $name,
      // 'department' => $department,
      'department' => $this->checkData($department),
      // 'location' => $countries,
      'location' => $this->checkData($request->country_city),
      // 'employement_type' => $empType,
      'employement_type' => $this->checkData($empType),
      // 'status' => $status,
      'status' => $this->checkData($status),
      'fromDate' => explode(" ", $fromDate)[0],
      'toDate' => explode(" ", $toDate)[0],
      'reportHeader' => $headers,
      'reportData' => $data
    ];
    // error_log($fullData);
    
    return $fullData;
  }

  public function checkData($request){
    if($request[0] == "All"){
      $data = $request[0];
      $val = array($data);
      // Log::debug($val);
    }else{
      $val = $request;
      // Log::debug($val);
    }
    return $val;
  }

  public function getEmployeeLeaveBalanceReportData(Request $request) {
    $data = Employee_leave_balance::from("employee_leave_balances as eb")
    ->join('users as ur', 'ur.employee_id', '=', 'eb.empid')
    ->join('leave_names as lm', 'lm.id', '=', 'eb.leave_id')
    ->join('employees as em', 'em.empid', '=', 'eb.empid')
    ->whereIn('lm.name', array_map('trim', ["Annual Leave Balance", "Sick Leave Balance"]))
    ->orderBy('em.location', 'desc')
    ->orderBy('em.empid')
    ->orderBy('eb.leave_id')
    ->get(['eb.empid', 'ur.name as ename', 'lm.name', 'eb.balance']);

    $formattedData = [];
    foreach($data as $eachData) {
      if(array_key_exists($eachData['empid'],$formattedData)) {
          $appendByLeave = $formattedData[$eachData['empid']];
          $appendByLeave['sbal'] = $eachData['balance'];
          $formattedData[$eachData['empid']] = $appendByLeave;
      }else {
          $region_name = substr($eachData['empid'],0,2);
          $leaveByEntry['country'] = $region_name;
          $leaveByEntry['name'] = $eachData['ename'];
          $leaveByEntry['abal'] = $eachData['balance'];
          $formattedData[$eachData['empid']] = $leaveByEntry;
      }
    }
    
    $headers = ['Country', 'Name', 'Annual Leave Balance', 'Sick Leave Balance'];

    $fullData = [
      'reportHeader' => $headers,
      'reportData' => $formattedData
    ];

    return $fullData;
  }

  public function getDetailedLeaveReportData(Request $request, $leaveType) {
    $formattedAnnualLeaveData = [];
    
    $from = $this->getFirstDayOfCurrentYear();
    $to = Carbon::now()->format('Y-m-d');

    $approvedLeaveDetails = Employees::from('employees as em')
    ->leftjoin('create_apply_leave_tables as ap', function ($join) use ($leaveType, $from, $to){
      $join->on('ap.empid', '=', 'em.empid');
      $join->where('ap.leave_type', 'like', $leaveType.'%');
      $join->where('ap.status', 'Approved');
      $join->where(function ($query) use ($from, $to){
          $query->whereBetween('ap.start_date', [$from, $to])  
                ->orWhereBetween('ap.end_date', [$from, $to]);
      });
      $join->orWhere(function ($query) use ($from, $to){
          $query->where('ap.start_date', '<=', $from)  
                ->where('ap.end_date', '>=', $to);
      });
    })
    ->leftjoin('users as ur', 'ur.employee_id', '=', 'em.empid')
      ->where('em.location', '!=', null)
      ->orderBy('em.location', 'desc')
      ->orderBy('em.empid')
      ->orderBy('ap.start_date')
      ->get(['ur.employee_id', 'ur.name', 'ap.start_date', 'ap.end_date', 'ap.duration_type']);
      
    foreach($approvedLeaveDetails as $eachData) {
      $empId = $eachData['employee_id'];
      if(array_key_exists($empId, $formattedAnnualLeaveData)) {
          $appendByEmpId = $formattedAnnualLeaveData[$empId];
          $appendByEmpId['days'] = array_merge_recursive($appendByEmpId['days'], $this->getDatesBetweenDateRange($eachData));
          $formattedAnnualLeaveData[$empId] = $appendByEmpId;
      }else {
          $region_name = substr($empId,0,2);
          $leaveByEntry['country'] = $region_name;
          $leaveByEntry['name'] = $eachData['name'];
          $leaveByEntry['days'] = $this->getDatesBetweenDateRange($eachData);
          $formattedAnnualLeaveData[$empId] = $leaveByEntry;
      }
    }

    $headers = ['Country', 'Name', 'Leave Days'];

    $fullData = [
      'reportHeader' => $headers,
      'reportData' => $formattedAnnualLeaveData
    ];

    return $fullData;
  }

  public function getEmployeeAllLeaveBalanceReportData() {
    $data = Employee_leave_balance::from("employee_leave_balances as eb")
    ->join('users as ur', 'ur.employee_id', '=', 'eb.empid')
    ->join('leave_names as lm', 'lm.id', '=', 'eb.leave_id')
    ->join('employees as em', 'em.empid', '=', 'eb.empid')
    ->orderBy('em.location', 'desc')
    ->orderBy('em.empid')
    ->orderBy('eb.leave_id')
    ->get(['eb.empid', 'ur.name as ename', 'lm.name', 'eb.balance']);

    $formattedData = [];
    foreach($data as $eachData) {
      if(array_key_exists($eachData['empid'],$formattedData)) {
          $appendByLeave = $formattedData[$eachData['empid']];
          $appendByLeave['bal'] = array_merge_recursive($appendByLeave['bal'], [$eachData['balance']]);
          $formattedData[$eachData['empid']] = $appendByLeave;
      }else {
          $leaveByEntry['empid'] = $eachData['empid'];
          $leaveByEntry['name'] = $eachData['ename'];
          $leaveByEntry['bal'] = [$eachData['balance']];
          $formattedData[$eachData['empid']] = $leaveByEntry;
      }
    }
    
    $headers = ['Country', 'Name', 'Annual Leave Balance', 'Sick Leave Balance', 'Optional Leave Balance', 'Compensatory Leave Balance', 'Marriage Leave Balance', 'Bereavement Leave Balance', 'Maternity Leave Balance', 'Paternity Leave Balance', 'Childcare Leave Balance', 'Carers Leave Balance', 'National Service Leave Balance', 'Hospital Leave Balance'];

    $fullData = [
      'reportHeader' => $headers,
      'reportData' => $formattedData
    ];

    return $fullData;
  }

  public function getDatesBetweenDateRange($leaveData) {
    $dates = [];
    $sd = $leaveData->start_date;
    $ed = $leaveData->end_date;
    if($sd != null and $ed != null) {
      $startDate = Carbon::parse($sd);
      $endDate = Carbon::parse($ed);
      $region_name = substr($leaveData->employee_id,0,2);

      $workingSat = $this->getFormattedWorkingSatDataFromDB();
      $holidays = $this->getFormattedHolidayDataFromDB();
  
      for($i = $startDate; $i <= $endDate; $i->modify('+1 day')){
        $today = Carbon::now();
        if($i->lte($today)) {
          $formattedDate = $i->format('Y-m-d');
          $insertFormattedDate = $i->formatLocalized('%d-%b-%y');
          if($leaveData->duration_type != 'Full Day') {
            $insertFormattedDate = $insertFormattedDate.'*';
          }
          if (date('N', strtotime($formattedDate)) < 6 and (array_key_exists($region_name, $holidays) and !(in_array($formattedDate, $holidays[$region_name])))) {
            array_push($dates, $insertFormattedDate);
          }
        }
      }
    }
    
    return $dates;
  }

  public function getFormattedHolidayDataFromDB() {
    $formattedHolidayByRegion = [];
    $holidays = holidaylist::from('holidaylists as hl')
        ->where('holidaytype', 'like', 'public%')
        ->get(['hl.region', 'hl.holidaydate']); 
    foreach($holidays as $eachData) {
      $region = $eachData['region'];
      $satDate = $eachData['holidaydate'];
      if(array_key_exists($region,$formattedHolidayByRegion)) {
          $appendByRegion = $formattedHolidayByRegion[$region];
          array_push($appendByRegion, $satDate);
          $formattedHolidayByRegion[$region] = $appendByRegion;
      }else {
          $dateByRegion = [];
          array_push($dateByRegion, $satDate);
          $formattedHolidayByRegion[$region] = $dateByRegion;
      }
    }

    return $formattedHolidayByRegion;
  }

  public function getFormattedWorkingSatDataFromDB() {
    $formattedWorkingSatByRegion = [];
    $workingSat = Workingsaturdays::from('workingsaturdays as ws')
        ->get(['ws.region', 'ws.holidaydate']);
     
    foreach($workingSat as $eachData) {
      $region = $eachData['region'];
      $satDate = $eachData['holidaydate'];
      if(array_key_exists($region,$formattedWorkingSatByRegion)) {
          $appendByRegion = $formattedWorkingSatByRegion[$region];
          array_push($appendByRegion, $satDate);
          $formattedWorkingSatByRegion[$region] = $appendByRegion;
      }else {
          $dateByRegion = [];
          array_push($dateByRegion, $satDate);
          $formattedWorkingSatByRegion[$region] = $dateByRegion;
      }
    }

    return $formattedWorkingSatByRegion;
  }

  public function getFirstDayOfCurrentYear() {
    $today = Carbon::now();
    $currentYear = $today->format('Y');
    $firstDay = Carbon::parse($currentYear.'-01-01')->format('Y-m-d');
    return $firstDay;
  }
}