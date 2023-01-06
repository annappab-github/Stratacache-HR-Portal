<?php

namespace App\Http\Controllers;

use App\Models\Gate;
use App\Exports\CsvExcelExport;
use PDF;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromView;
use OwenIt\Auditing\Models\Audit;
use Excel;
use DB;
use App\Models\Employees;

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
    $exporter = Excel::download(new CsvExcelExport, 'report.'.$fileFormat); 
    Log::debug($this->recordCount);  

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
    
    Log::debug(count($data));
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
    
    Log::debug(">>>>>>>>>>>>>>>>>>>> Record Count >>>>>>>>>>>>>>>>>>>".count($data));
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
      Log::debug($val);
    }else{
      $val = $request;
      Log::debug($val);
    }
    return $val;
  }
}