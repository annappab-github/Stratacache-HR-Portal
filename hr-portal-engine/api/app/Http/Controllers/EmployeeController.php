<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Excel;
use File;
use Storage;
use DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Intervention\Image\ImageManagerStatic as Image;
use App\Models\Employees;
use App\Models\Employee_leave_balance;
use App\Models\create_apply_leave_table;
use App\Models\holidaylist;
use App\Models\Leave_name;


class EmployeeController extends Controller
{
    // const GET_ALL_EMPLOYEES_DATA_FROM_EXCEL = [self::class, 'getAllEmployeesDataFromExcel'];    
    const GET_EMPLOYEE_DATA_BY_ID_FROM_EXCEL = [self::class, 'getEmployeeDataByEmployeeId'];
    const GET_EMPLOYEE_DASHBOARD_DATA = [self::class, 'getEmployeeDashboardData'];
    const GET_EMPLOYEE_INFO_DATA = [self::class, 'getEmployeeInfoData'];
    const GET_EMPLOYEE_LEAVE_DATA = [self::class, 'getEmployeeLeaveDetails'];
    const GET_EMPLOYEE_LEAVE_DATA_WITH_FILTERS = [self::class, 'getEmployeeLeaveDetailsWithFilter'];
    const UPDATE_EMPLOYEE_PROFILE_PHOTO = [self::class, 'updateEmployeeProfilePhoto'];


    public function getAllEmployeesDataFromExcel() {
        $excelPath = config('constants.employee_list_excel_path');
        $data = Excel::toArray([],$excelPath);
        return $this->formatEmployeeData($data[0]);
        // return $data[0];
    }   

    public function getEmployeeDataByEmployeeId($empId) {
        if(trim($empId) != '' || trim($empId) != null) {
            $empData = Employees::from('employees as em')
            ->join('users as ur', 'ur.employee_id', '=', 'em.empid')
            ->where('empid', '=', $empId)
            ->get(['em.id', 'em.empid', 'ur.name', 'ur.email', 'em.address','em.gender','em.employement_type', 'em.start_Date', 'em.birth_dates', 'em.employee_grade', 'em.location', 'em.city', 'em.position', 'em.department', 'em.land_ext', 'em.mobile_number', 'em.reporting_to', 'em.profile_pic', 'em.status']);
            $empData[0]['start_Date'] = Carbon::parse($empData[0]['start_Date'])->format('d-m-Y');
            $empData[0]['birth_dates'] = ($empData[0]['birth_dates'] != '' && $empData[0]['birth_dates'] != null ? Carbon::parse($empData[0]['birth_dates'])->format('d-m-Y') : '');
            $empData[0]['employeeShortName'] = $this->getEmployeeShortName(trim($empData[0]['name']));
            $empData[0]['imageBackgroundColour'] = $this->getImageBackgroundColour(trim($empData[0]['name']));
            $empData[0]['reportee_data'] = count($this->getDirectReporteeDetailsByEmployeeId($empId));
            return $empData;
        } else {
            return array();
        }
    }

    public function getDirectReporteeDetailsByEmployeeId($empId) {
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

    public function getCelebrationData($empId) {
        $allData = Employees::from('employees as em')
            ->join('users as ur', 'ur.employee_id', '=', 'em.empid')
            ->whereIn('status', array('active', 'consultant'))
            ->whereYear('start_Date', '<=', Carbon::now()->year)
            ->whereMonth('birth_dates', Carbon::now()->month)
            ->orWhereMonth('start_Date', Carbon::now()->month)
            ->get(['em.id', 'em.empid', 'ur.name', 'em.start_Date', 'em.birth_dates', 'em.profile_pic']);  
        $celebrationData = [];
        foreach($allData as $eachData) {
            if($eachData['birth_dates'] == "" || $eachData['birth_dates'] == null) {
                break;
            }
            $eachData['birth_dates'] = Carbon::parse($eachData['birth_dates'])->format('d-m-Y');
            $eachData['start_Date'] = Carbon::parse($eachData['start_Date'])->format('d-m-Y');
            $shortName = $this->getEmployeeShortName(trim($eachData['name']));
            $imageBackground = $this->getImageBackgroundColour(trim($eachData['name']));

            if($this->checkIfCurrentMonth($eachData['start_Date'])) {
                if(($eachData['empid'] == $empId) && ($this->checkIfLoggedInUserHasCelebration($eachData['start_Date'])) && !($this->checkIfCurrentYear($eachData['start_Date']))){
                    array_push($celebrationData, [$eachData['profile_pic'], $eachData['name'], $eachData['start_Date'], "HAPPY ANNIVERSARY", true, $shortName, $imageBackground, $eachData['empid']]);
                }else {
                    array_push($celebrationData, [$eachData['profile_pic'], $eachData['name'], $eachData['start_Date'], "HAPPY ANNIVERSARY", false, $shortName, $imageBackground, $eachData['empid']]);
                }
            }
            if($this->checkIfCurrentMonth($eachData['birth_dates'])) {
                if(($eachData['empid'] == $empId) && ($this->checkIfLoggedInUserHasCelebration($eachData['birth_dates']))){
                    array_push($celebrationData, [$eachData['profile_pic'], $eachData['name'], $eachData['birth_dates'], "HAPPY BIRTHDAY", true, $shortName, $imageBackground, $eachData['empid']]);
                }else {
                    array_push($celebrationData, [$eachData['profile_pic'], $eachData['name'], $eachData['birth_dates'], "HAPPY BIRTHDAY", false, $shortName, $imageBackground, $eachData['empid']]);
                }
            }
        }

        return collect($celebrationData)->sortBy(function ($temp, $key) {
            return Carbon::parse($temp[2])->format('m-d');
        });
    }

    public function getEmployeeDashboardData(Request $request) {
        $dashboardData = [];
        $celebrationIndexSorted = [];
        $celebrationResponse = $this->getCelebrationData($request->empId);
        foreach($celebrationResponse as $eachCelebration) {
            array_push($celebrationIndexSorted, $eachCelebration);
        }

        $employeeDetails = $this->getEmployeeDataByEmployeeId($request->empId);

        array_push($dashboardData, ["employeeData" => $employeeDetails]);
        array_push($dashboardData, ["celebrationData" => $celebrationIndexSorted]);
        array_push($dashboardData, ["announcementData" => $this->getAllAnnouncements($request->empId)]);
        array_push($dashboardData, ["holidayData" => $this->getHolidayList()]);
        array_push($dashboardData, ["leaveBalanceData" => $this->getLeaveBalance($request->empId)]);
        array_push($dashboardData, ["whoIsOutData" => $this->getWhoIsOutData()]);
        $managerData = [];
        if($employeeDetails[0]['reporting_to'] != "")
            $managerData = $this->getEmployeeDataByEmployeeId($employeeDetails[0]['reporting_to']);
        array_push($dashboardData, ["managerData" => $managerData]);
        
        return $dashboardData;
    }

    public function getEmployeeInfoData(Request $request) {
        $myInfoData = [];
        
        $employeeDetails = $this->getEmployeeDataByEmployeeId($request->empId);

        array_push($myInfoData, ["employeeData" => $employeeDetails]);
        array_push($myInfoData, ["leaveBalanceData" => $this->getLeaveBalanceForMyInfo($request->empId)]);
        // array_push($myInfoData, ["upcomingLeaveData" => $this->getEmployeeLeaveDetails($request->fetchType, $request->year, $request->empId)]);
        array_push($myInfoData, ["upcomingLeaveData" => $this->getEmployeeLeaveDetails($request->fetchType,$request->year, $request->empId)]);
        array_push($myInfoData, ["upcomingHolidayData" => $this->getHolidayListByCountry($request->empId)]);
        array_push($myInfoData, ["managerData" => $this->getEmployeeDataByEmployeeId($employeeDetails[0]['reporting_to'])]);
        array_push($myInfoData, ["directReportsData" => $this->getDirectReporteeDetailsByEmployeeId($request->empId)]);
        

        return $myInfoData;
    }

    public function getEmployeeLeaveBalance(Request $request){
        $myInfoData = [];
        array_push($myInfoData, ["leaveBalanceData" => $this->getLeaveBalance($request->empId)]);
        return $myInfoData;
    }
    public function getManagerDirectReportees(Request $request){
        $myInfoData = [];
        $employeeDetails = $this->getEmployeeDataByEmployeeId($request->empid);
        array_push($myInfoData, ["employeeData" => $employeeDetails]);
        array_push($myInfoData, ["directReportsData" => $this->getDirectReporteeDetailsByEmployeeId($request->empid)]);


        $reporteesId = $this->getDirectReporteeDetailsByEmployeeId($request->empid);
        $reporteesIdArr = [];
        $reportLeaveBalData = [];
        foreach($reporteesId as $id){
            array_push($reporteesIdArr, ["reporteesIdArr" =>$id->empid]);
            $getReporteesLeaveBal =  $this->getLeaveBalance($id->empid);
            array_push($reportLeaveBalData, ["getReporteesLeaveBal" => $getReporteesLeaveBal]);
        }
            array_push($myInfoData, $reportLeaveBalData);
        // Log::debug($myInfoData);
        return $myInfoData;
    }

    public function updateEmployeeProfilePhoto(Request $request) {

        $attachment = '';
        if($request->hasFile('file')){
            $attachment = $request->File('file');
        }

        $empId = $request->empId;
        $uploadedFileName = $attachment->getClientOriginalName();
        $extension = explode(".", $uploadedFileName)[1];
        // $fileName = $empId.".".$extension;
        $fileName = $empId.".png";


        $image_resize = Image::make($attachment);              
        $image_resize->resize(420, 420)->encode('png',80);

        $base64 = 'data:image/;base64,' . base64_encode(file_get_contents($attachment));
        $existingEmployee = Employees::findOrFail($request->id);

        $existingEmployee->profile_pic = $base64;
        $existingEmployee->save();
        // Storage::disk('local')->put($fileName, $image_resize);
        // return base64_encode(file_get_contents(config('constants.employee_photo_path')."/".$fileName));
    }

    public function getLeaveBalance($empId) {
        $leaveData = [];

        $empLeaveData = Leave_name::from('leave_names as na')
        ->leftjoin('employee_leave_balances as em', function ($join) use ($empId){
            $join->on('em.leave_id', '=', 'na.id');
            $join->where('em.empid', '=', $empId);
        })
        ->get(['na.id as leave_name_id','em.empid','na.name','em.balance']);

        return $empLeaveData;
    }

    public function getLeaveBalanceForMyInfo($empId) {
        
        $empLeaveData = Employee_leave_balance::from('employee_leave_balances as em')
        ->join('leave_names as na', 'na.id', '=', 'em.leave_id')
        ->where(function ($outerQuery) use ($empId) {
            $outerQuery->whereIn('em.leave_id', [1,2,3])
            ->orWhereIn('em.leave_id', function ($query) use ($empId){
                $query->select(DB::raw(1))
                    ->from('create_apply_leave_tables as ca')
                    ->where('ca.empid', $empId)
                    ->where('ca.status', 'Approved')
                    ->where(function ($subQuery) {
                        $subQuery->whereYear('ca.start_date', Carbon::now()->year)
                        ->orWhereYear('ca.end_date', Carbon::now()->year);
                    })
                    ->select('ca.leave_id')
                    ->distinct();
            });
        })
        ->where('empid', '=', $empId)
        ->get(['na.id as leave_name_id','em.empid','na.name','em.balance']);

        return $empLeaveData;
    }

    public function getWhoIsOutData(){
        $whoIsOutData = [];
        $outToday = [];
        $outTomorrow = [];
        $outThisMonth = [];

        $today = Carbon::today()->modify('now');
        $tomorrow = Carbon::today()->modify('now')->addDays(1);
        $dayAfterTomorrow = Carbon::today()->modify('now')->addDays(2);

        $outToday = create_apply_leave_table::from('create_apply_leave_tables as ap')
        ->join('employees as em', 'em.empid', '=', 'ap.empid')
        ->join('users as ur', 'ur.employee_id', '=', 'em.empid')
        ->where('ap.status', '=', 'Approved')
        ->where('ap.start_date', '<=', $today)
        ->where('ap.end_date', '>=', $today)
        ->orderBy('ur.name')
        ->select('em.empid', 'ur.name', 'ap.duration_type', 'em.profile_pic')
        ->get();

        if(count($outToday) > 0){
            foreach($outToday as $eachEmp) {
                $eachEmp['employeeShortName'] = $this->getEmployeeShortName(trim($eachEmp['name']));
                $eachEmp['imageBackgroundColour'] = $this->getImageBackgroundColour(trim($eachEmp['name']));
                if($eachEmp['duration_type'] != 'Full Day') {
                    $eachEmp['name'] = $eachEmp['name']." (".$eachEmp['duration_type'].")"; 
                }
            }
        }
        // Log::debug($outToday);

        $outTomorrow = create_apply_leave_table::from('create_apply_leave_tables as ap')
        ->join('employees as em', 'em.empid', '=', 'ap.empid')
        ->join('users as ur', 'ur.employee_id', '=', 'em.empid')
        ->where('ap.status', '=', 'Approved')
        ->where('ap.start_date', '<=', $tomorrow)
        ->where('ap.end_date', '>=', $tomorrow)
        ->orderBy('ur.name')
        ->select('em.empid', 'ur.name', 'ap.duration_type', 'em.profile_pic')
        ->get();

        if(count($outTomorrow) > 0){
            foreach($outTomorrow as $eachEmp) {
                $eachEmp['employeeShortName'] = $this->getEmployeeShortName(trim($eachEmp['name']));
                $eachEmp['imageBackgroundColour'] = $this->getImageBackgroundColour(trim($eachEmp['name']));
                if($eachEmp['duration_type'] != 'Full Day') {
                    $eachEmp['name'] = $eachEmp['name']." (".$eachEmp['duration_type'].")"; 
                }
            }
        }
        // Log::debug($outTomorrow);

        $outThisMonth = create_apply_leave_table::from('create_apply_leave_tables as ap')
        ->join('employees as em', 'em.empid', '=', 'ap.empid')
        ->join('users as ur', 'ur.employee_id', '=', 'em.empid')
        ->where('ap.status', '=', 'Approved')
        ->whereMonth('ap.start_date', Carbon::now()->month)
        ->where('ap.end_date', '>=', $dayAfterTomorrow)
        ->orderBy('ap.start_date')
        ->orderBy('ur.name')
        ->select('em.empid', 'ur.name', 'em.profile_pic', 'ap.start_date', 'ap.end_date')
        ->get();

        if(count($outThisMonth) > 0){
            foreach($outThisMonth as $eachEmp) {
                $eachEmp['employeeShortName'] = $this->getEmployeeShortName(trim($eachEmp['name']));
                $eachEmp['imageBackgroundColour'] = $this->getImageBackgroundColour(trim($eachEmp['name']));

                $start = "";
                $end = "";
                //Calculating start date for rest of the month
                if($eachEmp['start_date'] <= $dayAfterTomorrow) {
                    $start = $dayAfterTomorrow->format('d');
                } else {
                    $start = Carbon::parse($eachEmp['start_date'])->format('d');
                }
                //Calculating end date for rest of the month
                $monthEnd = Carbon::parse($today)->endOfMonth()->format('Y-m-d');
                if($eachEmp['end_date'] <= $monthEnd){
                    $end = Carbon::parse($eachEmp['end_date'])->format('d');
                } else {
                    $end = Carbon::parse($monthEnd)->format('d');
                }
                //Appending dates to name
                if($start == $end)
                    $eachEmp['name'] = $eachEmp['name']." (".$start.")";
                else
                    $eachEmp['name'] = $eachEmp['name']." (".$start."-".$end.")";   
            }
        }
       
        array_push($whoIsOutData, ["today" => $outToday, "tomorrow" => $outTomorrow, "thisMonth" => $outThisMonth]);
        return $whoIsOutData;
    }

    public function getAllAnnouncements($empId) {
        $excelPath = config('constants.announcement_list_excel_path');
        $data = Excel::toArray([],$excelPath);
        return $this->formatAnnouncementData($data[0],$empId);
    }

    public function getHolidayList() {
        $formattedHolidayData = [];

        $startDate = Carbon::today()->modify('now');
        $endDate = Carbon::today()->modify('now')->addDays(30);
        $holidays = holidaylist::from('holidaylists as hl')
        ->whereBetween('hl.holidaydate', [$startDate, $endDate])
        ->orderBy('hl.holidaydate')
        ->orderBy('hl.region')
        ->get();

        foreach($holidays as $eachHoliday) {
            $holidayDate = Carbon::parse($eachHoliday->holidaydate);
            $monthDay = $holidayDate->format('F')." ".$holidayDate->format('d');
            $region = trim($eachHoliday->region);
            $holidaytype = trim($eachHoliday->holidaytype);

            if(array_key_exists($monthDay,$formattedHolidayData)) {
                $appendData = $formattedHolidayData[$monthDay];
                if(array_key_exists($holidaytype,$appendData)) {
                    $appendByHolidayType = $formattedHolidayData[$monthDay][$holidaytype];
                    $holidayName = $eachHoliday->holidayname;
                    if(in_array($holidayName, array_column($appendByHolidayType, 'holiday'))) {
                        $dataByHolidayName = $appendByHolidayType[key($appendByHolidayType)];
                        $dataByHolidayName['region'] = $dataByHolidayName['region'].",".$region;
                        $appendByHolidayType[key($appendByHolidayType)] = $dataByHolidayName;
                    } else {
                        array_push($appendByHolidayType, ['holiday' => trim($eachHoliday->holidayname), 'region' => $region]);
                    }
                    $appendData[$holidaytype] = $appendByHolidayType;
                }else {
                    $holidayByDay = [];
                    array_push($holidayByDay, ['holiday' => trim($eachHoliday->holidayname), 'region' => $region]);
                    $appendData[$holidaytype] = $holidayByDay;
                }
                
                $formattedHolidayData[$monthDay] = $appendData;
            } else {
                $holidayByDay = [];
                array_push($holidayByDay, ['holiday' => trim($eachHoliday->holidayname), 'region' => $region]);
                $formattedHolidayData[$monthDay][$holidaytype] = $holidayByDay;
            }
        }

        return $formattedHolidayData;
    }
    
    public function getHolidayListByCountry($empId) {
        $formattedHolidayData = [];

        $userRegion = substr($empId, 0, 2);
        $holidays = holidaylist::from('holidaylists as hl')
        ->where('hl.region', $userRegion)
        ->orderBy('hl.holidaydate')
        ->get();

        foreach($holidays as $eachHoliday) {
            $holidayDate = Carbon::parse($eachHoliday->holidaydate);
            $monthDay = $holidayDate->format('F')." ".$holidayDate->format('d');
            $holidayByDay = [];
            array_push($holidayByDay, ['day' => $monthDay, 'type' => trim($eachHoliday->holidaytype), 'reason' => trim($eachHoliday->holidayname)]);
            $formattedHolidayData[$monthDay] = $holidayByDay;
        }    

        return $formattedHolidayData;
    }
    
    public function getEmployeeLeaveDetails($fetchType, $year, $empId){
        $today = Carbon::now()->format('Y-m-d');
        $data = [];
        if($fetchType == "upcoming") {
            $data = create_apply_leave_table::from('create_apply_leave_tables as ap')
            ->where('ap.empid', '=', $empId)
            ->where(function ($query) use ($today){
                $query->where('ap.start_date', '>=', $today)
                    ->orWhere('ap.end_date', '>=', $today);
            })
            ->orderBy('ap.start_date')
            ->get(['ap.leave_type','ap.empid','ap.comments','ap.duration_type','ap.start_date','ap.end_date','ap.reason','ap.status','ap.num_of_days', 'ap.id','ap.leave_id', 'ap.updated_at']);  
        } else {
            $data = create_apply_leave_table::from('create_apply_leave_tables as ap')
            ->where('ap.empid', '=', $empId)
            ->where('ap.end_date', '<', $today)
            ->where(function ($query) use ($today){
                $query->whereYear('ap.start_date', Carbon::now()->year)
                    ->orWhereYear('ap.end_date', Carbon::now()->year);
            })
            ->orderBy('ap.start_date', 'desc')
            ->get(['ap.leave_type','ap.empid','ap.comments','ap.duration_type','ap.start_date','ap.end_date','ap.reason','ap.status','ap.num_of_days', 'ap.id','ap.leave_id',]);  
        }
        
        return $data;
    }

    public function getEmployeeLeaveDetailsWithFilter(Request $request) {
        $monthArray = ['Jan' => 1, 'Feb' => 2, 'Mar' => 3, 'Apr' => 4, 'May' => 5, 'Jun' => 6, 'Jul' => 7, 'Aug' => 8, 'Sep' => 9, 'Oct' => 10, 'Nov' => 11, 'Dec' => 12];
        $empId = $request->empId;
        $year = $request->year;
        $month = $request->month;
        $leaveType = $request->type;
        if($leaveType == 'Earned') {
            $leaveType = 'Annual';
        }
        $today = Carbon::now()->format('Y-m-d');

        $data = create_apply_leave_table::from('create_apply_leave_tables as ap')
        ->where('ap.empid', '=', $empId)
        ->where('ap.end_date', '<', $today)
        ->where(function ($query) use ($leaveType){
            if($leaveType != 'All') {
                $query->where('ap.leave_type', 'like', $leaveType.'%');
            }
        })
        ->where(function ($query) use ($month, $monthArray){
            if($month != 'All') {
                $query->whereMonth('ap.start_date', $monthArray[$month])
                    ->orWhereMonth('ap.end_date', $monthArray[$month]);
            }
        })
        ->where(function ($query) use ($year){
            $query->whereYear('ap.start_date', $year)
                ->orWhereYear('ap.end_date', $year);
        })
        ->orderBy('ap.start_date', 'desc')
        ->get(['ap.leave_type','ap.empid','ap.comments','ap.duration_type','ap.start_date','ap.end_date','ap.reason','ap.status','ap.num_of_days', 'ap.id','ap.leave_id',]);  
    
        return $data;
    }

    // public function getEmployeeLeaveDetails($fetchType, $year, $empId){
    //     $formattedLeaveData = [];
    //     $convertLeaveType = '';

    //     $data = create_apply_leave_table::from('create_apply_leave_tables as ap')
    //     ->where('ap.empid', '=', $empId)
    //     // ->get(['ap.leave_type','ap.empid','ap.duration_type','ap.start_date','ap.end_date','ap.reason','ap.status','ap.num_of_days']);
    //     ->get(['ap.leave_type','ap.empid','ap.comments','ap.duration_type','ap.start_date','ap.end_date','ap.reason','ap.status','ap.num_of_days', 'ap.id','ap.leave_id',]);
        
    //     $currentYear = Carbon::now()->format('Y');
    //     $today = Carbon::today()->modify('now');

    //     if($year == $currentYear){
    //         // Log::debug('current');
    //         foreach($data as $eachLeave) {
    //             if($eachLeave->empid != null && $eachLeave->empid == $empId){

    //             $validRecord = false;
    //             $month = date("F", strtotime($eachLeave->start_date));

    //             $splitStartDate = explode("-", $eachLeave->start_date);
    //             $startDay = $splitStartDate[2];
    //             $splitEndDate = explode("-", $eachLeave->end_date);
    //             $endDay = $splitEndDate[2];

    //             $day = $startDay;
    //             if($eachLeave->start_date != $eachLeave->end_date) {
    //                 $day = $day."-".$endDay;
    //             }

    //             if($fetchType == 'history'){
    //                 if($today >= $eachLeave->end_date) {
    //                     $validRecord = true;
    //                 }
    //             }else {
    //                 if($eachLeave->start_date >= $today || $eachLeave->end_date >= $today) {
    //                     $validRecord = true;
    //                 }
    //             }

    //             if($eachLeave->leave_type == 'Annual Leave Balance'){
    //                 $convertLeaveType = 'Earned';
    //             }else{
    //                 $split = (explode(" ",$eachLeave->leave_type));
    //                 $convertLeaveType = $split[0]." ".$split[1];
    //             }
    //             // Log::debug($eachLeave);
    //             if($validRecord) {
    //                 if(array_key_exists($month,$formattedLeaveData)) {
    //                     $appendByLeave = $formattedLeaveData[$month];
    //                     array_push($appendByLeave, ['day' => $day, 'start_date' =>$eachLeave->start_date, 'end_date' =>$eachLeave->end_date,  'type' => $convertLeaveType ,'reason' => $eachLeave->reason, 'status' => $eachLeave->status, 'noofdays' => $eachLeave->num_of_days, 'duration' => $eachLeave->duration_type, 'id' => $eachLeave->id ,'leave_id' =>$eachLeave->leave_id,'comments' => $eachLeave->comments] );
    //                     $formattedLeaveData[$month] = $appendByLeave;
    //                 }else {
    //                     $leaveByEntry = [];
    //                     array_push($leaveByEntry, ['day' => $day, 'start_date' =>$eachLeave->start_date, 'end_date' =>$eachLeave->end_date,'type' => $convertLeaveType ,'reason' => $eachLeave->reason, 'status' => $eachLeave->status, 'noofdays' => $eachLeave->num_of_days, 'duration' => $eachLeave->duration_type, 'id' => $eachLeave->id,'leave_id' =>$eachLeave->leave_id,'comments' => $eachLeave->comments]);
    //                     $formattedLeaveData[$month] = $leaveByEntry;
    //                 }
    //             }
    //             }   
    //         }
    // }
    // else {
    //     foreach($data as $eachLeave) {
    //         if($eachLeave->empid != null && $eachLeave->empid == $empId){
    //             $month = date("F", strtotime($eachLeave->start_date));

    //             $splitStartDate = explode("-", $eachLeave->start_date);
    //             $startDay = $splitStartDate[2];
    //             $splitEndDate = explode("-", $eachLeave->end_date);
    //             $endDay = $splitEndDate[2];

    //             $day = $startDay;
    //             if($eachLeave->start_date != $eachLeave->end_date) {
    //                 $day = $day."-".$endDay;
    //             }

    //             if(array_key_exists($month,$formattedLeaveData)) {
    //                 $appendByLeave = $formattedLeaveData[$month];
    //                 array_push($appendByLeave, ['day' => $day, 'type' => $convertLeaveType ,'reason' => $eachLeave->reason, 'status' => $eachLeave->status, 'noofdays' => $eachLeave->num_of_days , 'duration' => $eachLeave->duration_type, 'id' => $eachLeave->id,'leave_id' =>$eachLeave->leave_id,'comments' => $eachLeave->comments]);
    //                 $formattedLeaveData[$month] = $appendByLeave;
    //             }else {
    //                 $leaveByEntry = [];
    //                 array_push($leaveByEntry, ['day' => $day, 'type' => $convertLeaveType ,'reason' => $eachLeave->reason, 'status' => $eachLeave->status, 'noofdays' => $eachLeave->num_of_days, 'duration' => $eachLeave->duration_type, 'id' => $eachLeave->id,'leave_id' =>$eachLeave->leave_id,'comments' => $eachLeave->comments]);
    //                 $formattedLeaveData[$month] = $leaveByEntry;
    //             }
    //         }
    //     }

    // }
    //     return $formattedLeaveData;
    // }

    public function formatEmployeeData($excelData) {
        $managerReporteeArray = $this->getAllEmployeeDataJSON($excelData);
        $formattedArray = [];
        foreach(array_slice($excelData,1) as $eachData) {
            if($eachData[1] != null) {
                $eachData[2] = $this->formatDate($eachData[2]);
                $eachData[12] = $this->formatDate($eachData[12]);
                $eachData[14] = Carbon::parse($eachData[2])->addMonths((int)$eachData[13])->format('d-m-Y');

                try {
                    // $eachData[17] = base64_encode(file_get_contents(config('constants.employee_photo_path')."/".$eachData[1].".png"));   
                    $base64ImgPath = Employees::where('empid',$eachData[1])->get('profile_pic');
                    $imgPath = explode( ',', $base64ImgPath[0]->profile_pic);
                    //Log::debug($imgPath);
                    $eachData[17] = $imgPath[1];
                } catch (\Exception $e) {
                    // $eachData[17] = base64_encode(file_get_contents(config('constants.employee_photo_path')."/imagealt.png"));
                    $eachData[17] = 'noimage';
                }
                
                $eachData[18] = $this->getEmployeeShortName(trim($eachData[4]));
                $eachData[19] = $this->getImageBackgroundColour(trim($eachData[4]));

                $manager = "";
                if($eachData[11] != null)
                    $manager = $managerReporteeArray[trim($eachData[11])];

                $eachData[20] = $manager; 
                
                $reporteeCount = "";
                if(array_key_exists(trim($eachData[1])."_r",$managerReporteeArray))
                    $reporteeCount = $managerReporteeArray[trim($eachData[1])."_r"];
                $eachData[21] = $reporteeCount;
                array_push($formattedArray, $eachData);
            }
        }
        
        return $formattedArray;
    }

    public function getAllEmployeeDataJSON($excelData) {
        $managerReporteeArray = [];
        $reporteeArray = [];

        foreach(array_slice($excelData,1) as $eachData) {
            if($eachData[1] != null) {
                $managerReporteeArray[trim($eachData[1])] = trim($eachData[4]);
            }
            if($eachData[11] != null) {
                $key = trim($eachData[11])."_r";
                if(array_key_exists($key,$reporteeArray)) {
                    $count = $reporteeArray[$key];
                    $reporteeArray[$key] = $count + 1;
                } else {
                    $reporteeArray[$key] = 1;
                }
            }
        }
        
        return array_merge($managerReporteeArray,$reporteeArray);
    }

    public function formatAnnouncementData($excelData,$empId) {
        $formattedArray = [];
        foreach(array_slice($excelData,1) as $eachData) {
            if($eachData[1] != null) {
                $eachData[3] = $this->formatDate($eachData[3]);
                $eachData[4] = $this->formatDate($eachData[4]);
                if($this->checkRegion($empId,trim($eachData[2])) && $this->validateAnnoucementDate($eachData[3],$eachData[4])) {
                    array_push($formattedArray, $eachData);
                }
            }
        }

        return $formattedArray;
    }

    public function formatDate($excelDate) {
        $UNIX_DATE = ((int)$excelDate - 25569) * 86400;
        $formattedDate = gmdate("d-m-Y", $UNIX_DATE);
        return $formattedDate;
    }    

    public function checkIfCurrentMonth($date) {
        $today = Carbon::now()->format('m');
        $inputDate = Carbon::parse($date)->format('m');
        if($today == $inputDate) {
            $day = Carbon::now()->format('d');
            $inputDay = Carbon::parse($date)->format('d');

            return $inputDay >= $day;
        }
        return false;
    }

    public function checkIfCurrentYear($date) {
        $today = Carbon::now()->format('Y');
        $inputDate = Carbon::parse($date)->format('Y');
        return $today == $inputDate;
    }

    public function checkIfLoggedInUserHasCelebration($date) {
        $today = Carbon::now()->format('d-m');
        $inputDate = Carbon::parse($date)->format('d-m');
        
        return $today == $inputDate;
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

    public function checkIfDateInRange($holidayDate) {
        $startDate = Carbon::today()->modify('now');
        $endDate = Carbon::today()->modify('now')->addDays(15);
        $holidayDate = Carbon::parse($holidayDate);
        return $holidayDate->between($startDate,$endDate);
    }

    public function validateAnnoucementDate($sDate,$eDate) {
        $today = Carbon::today()->modify('now');
        $startDate = Carbon::parse($sDate);
        $endDate = Carbon::parse($eDate);
        return $today->between($startDate,$endDate);
    }

    public function checkByLeaveType($leaveType,$date) {

        switch($leaveType) {
            case("Full Day"):
                return true;
                break;
                
            case("First Half"):
                return $this->checkCurrentTime($leaveType,$date);
                break;

            case("Second Half"):
                return $this->checkCurrentTime($leaveType,$date);  
                break;
        }
        
        return false;
    }

    public function checkCurrentTime($leaveType,$date) {
        $currentTime = Carbon::now();
        $fh = $date." "."07:30";
        $firstHalf = Carbon::parse($fh)->setTimezone('UTC');
    
        if($leaveType == "First Half") {
            return $currentTime->lte($firstHalf);
        }

        return $currentTime->gt($firstHalf);
    }

    public function getAllDatesBetweenRange($startDate,$endDate) {
        $range = CarbonPeriod::create($startDate,$endDate);
        $dates = [];
        foreach($range as $eachDate) {
            array_push($dates, $eachDate->format('d'));
        }
        
        return implode (",", $dates);
    }

    public function checkRegion($empId,$region) {
        $regionList = explode(',', $region);
        $userRegion = substr($empId, 0, 2);
        foreach($regionList as $eachRegion) {
            if(strtolower(trim($eachRegion)) == "all" || strtolower(trim($eachRegion)) == strtolower($userRegion))
                return true;
        }

        return false;
    }
}
