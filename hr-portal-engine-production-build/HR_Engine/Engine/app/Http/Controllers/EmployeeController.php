<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Excel;
use File;
use Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Intervention\Image\ImageManagerStatic as Image;
use App\Models\Employees;


class EmployeeController extends Controller
{
    // const GET_ALL_EMPLOYEES_DATA_FROM_EXCEL = [self::class, 'getAllEmployeesDataFromExcel'];    
    const GET_EMPLOYEE_DATA_BY_ID_FROM_EXCEL = [self::class, 'getEmployeeDataByEmployeeId'];
    const GET_EMPLOYEE_DASHBOARD_DATA = [self::class, 'getEmployeeDashboardData'];
    const GET_EMPLOYEE_INFO_DATA = [self::class, 'getEmployeeInfoData'];
    const GET_EMPLOYEE_LEAVE_DATA = [self::class, 'getEmployeeLeaveDetails'];
    const UPDATE_EMPLOYEE_PROFILE_PHOTO = [self::class, 'updateEmployeeProfilePhoto'];


    public function getAllEmployeesDataFromExcel() {
        $excelPath = config('constants.employee_list_excel_path');
        $data = Excel::toArray([],$excelPath);
        return $this->formatEmployeeData($data[0]);
        // return $data[0];
    }   

    public function getEmployeeDataByEmployeeId($empId) {
        $empData = Employees::from('employees as em')
        ->join('users as ur', 'ur.employee_id', '=', 'em.empid')
        ->where('empid', '=', $empId)
        ->get(['em.id', 'em.empid', 'ur.name', 'ur.email', 'em.address', 'em.start_Date', 'em.birth_dates', 'em.employee_grade', 'em.location', 'em.city', 'em.position', 'em.department', 'em.land_ext', 'em.mobile_number', 'em.reporting_to', 'em.profile_pic', 'em.status']);
        $empData[0]['start_Date'] = Carbon::parse($empData[0]['start_Date'])->format('d-m-Y');
        $empData[0]['birth_dates'] = ($empData[0]['birth_dates'] != '' && $empData[0]['birth_dates'] != null ? Carbon::parse($empData[0]['birth_dates'])->format('d-m-Y') : '');
        $empData[0]['employeeShortName'] = $this->getEmployeeShortName(trim($empData[0]['name']));
        $empData[0]['imageBackgroundColour'] = $this->getImageBackgroundColour(trim($empData[0]['name']));
        return $empData;
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
        array_push($myInfoData, ["leaveBalanceData" => $this->getLeaveBalance($request->empId)]);
        array_push($myInfoData, ["upcomingLeaveData" => $this->getEmployeeLeaveDetails($request->fetchType, $request->year, $request->empId)]);
        array_push($myInfoData, ["upcomingHolidayData" => $this->getHolidayListByCountry($request->empId)]);
        array_push($myInfoData, ["managerData" => $this->getEmployeeDataByEmployeeId($employeeDetails[0]['reporting_to'])]);
        array_push($myInfoData, ["directReportsData" => $this->getDirectReporteeDetailsByEmployeeId($request->empId)]);
        

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
        $excelPath = config('constants.leave_balance_excel_path');
        $data = Excel::toArray([],$excelPath);

        foreach(array_slice($data[0],1) as $eachData) {
            if($eachData[0] == $empId) {
                array_push($leaveData, $eachData);
                break;
            }
        }
        return $leaveData;
    }

    public function getWhoIsOutData() {
        $whoIsOutData = [];
        $outToday = [];
        $outTomorrow = [];
        $outThisMonth = [];
        $currentYear = Carbon::now()->format('Y');
        $excelPath = config('constants.leave_list_base_path')."/".$currentYear."/".config('constants.leave_list_excel_name');
        $data = Excel::toArray([],$excelPath);

        foreach(array_slice($data[0],1) as $eachData) {
            $today = Carbon::today()->modify('now');
            $tomorrow = Carbon::today()->modify('now')->addDays(1);
            $dayAfterTomorrow = Carbon::today()->modify('now')->addDays(2);
            $startDate = Carbon::parse($this->formatDate($eachData[4]));
            $endDate = Carbon::parse($this->formatDate($eachData[5]));
            
            $shortName = $this->getEmployeeShortName(trim($eachData[1]));
            $imageBackground = $this->getImageBackgroundColour(trim($eachData[1]));

            try {
                $eachData[7] = base64_encode(file_get_contents(config('constants.employee_photo_path')."/".$eachData[0].".png"));   
            } catch (\Exception $e) {
                $eachData[7] = 'noimage';
            }

            if ($today->between($startDate,$endDate) && $this->checkByLeaveType($eachData[6], $this->formatDate($eachData[4]))) {
                array_push($outToday, [$eachData[7], $eachData[0], $eachData[1], $shortName, $imageBackground]);
            } 
            if($tomorrow->between($startDate,$endDate)){
                array_push($outTomorrow, [$eachData[7], $eachData[0], $eachData[1], $shortName, $imageBackground]);
            } 
            if(($this->checkIfCurrentMonth($startDate) && $startDate->gte($dayAfterTomorrow)) || ($this->checkIfCurrentMonth($endDate) && $endDate->gte($dayAfterTomorrow))) {
                if($this->checkIfCurrentMonth($startDate) && $startDate->gte($dayAfterTomorrow)){
                    if($startDate->eq($endDate)) {
                        array_push($outThisMonth, [$eachData[7], $eachData[0], $eachData[1]." (".$startDate->format('d').")", $shortName, $imageBackground]);
                    } else {
                        array_push($outThisMonth, [$eachData[7], $eachData[0], $eachData[1]." (".$startDate->format('d')."-".$endDate->format('d').")", $shortName, $imageBackground]);
                    }
                } else {
                    if($dayAfterTomorrow->eq($endDate)) {
                        array_push($outThisMonth, [$eachData[7], $eachData[0], $eachData[1]." (".$dayAfterTomorrow->format('d').")", $shortName, $imageBackground]);
                    } else {
                        array_push($outThisMonth, [$eachData[7], $eachData[0], $eachData[1]." (".$dayAfterTomorrow->format('d')."-".$endDate->format('d').")", $shortName, $imageBackground]);
                    }
                }
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
        $currentYear = Carbon::now()->format('Y');

        $holidayPath = config('constants.holidays_base_path')."/".$currentYear."/".config('constants.holiday_excel_name');
        $holidayData = Excel::toArray([],$holidayPath);

        foreach(array_slice($holidayData[0],1) as $eachHoliday) {
            $monthDay = trim($eachHoliday[0])." ".trim($eachHoliday[1]);
            $formattedHolidayDate = $currentYear."-".date("m", strtotime(trim("$eachHoliday[0] 1 2022")))."-".trim($eachHoliday[1]);
            if($this->checkIfDateInRange($formattedHolidayDate)){
                if(array_key_exists($monthDay,$formattedHolidayData)) {
                    $appendData = $formattedHolidayData[$monthDay];
                    $holidaytype = trim($eachHoliday[3]);
                    if(array_key_exists($holidaytype,$appendData)) {
                        $appendByHolidayType = $formattedHolidayData[$monthDay][$holidaytype];
                        array_push($appendByHolidayType, ['holiday' => trim($eachHoliday[2]), 'region' => trim($eachHoliday[4])]);
                        $appendData[$holidaytype] = $appendByHolidayType;
                    }else {
                        $holidayByDay = [];
                        array_push($holidayByDay, ['holiday' => trim($eachHoliday[2]), 'region' => trim($eachHoliday[4])]);
                        $appendData[$holidaytype] = $holidayByDay;
                    }
                    
                    $formattedHolidayData[$monthDay] = $appendData;
                } else {
                    $holidayByDay = [];
                    array_push($holidayByDay, ['holiday' => trim($eachHoliday[2]), 'region' => trim($eachHoliday[4])]);
                    $formattedHolidayData[$monthDay][trim($eachHoliday[3])] = $holidayByDay;
                }
            }
        }
        return $formattedHolidayData;
    }

    public function getHolidayListByCountry($empId) {
        $formattedHolidayData = [];
        $currentYear = Carbon::now()->format('Y');

        $holidayPath = config('constants.holidays_base_path')."/".$currentYear."/".config('constants.holiday_excel_name');
        $holidayData = Excel::toArray([],$holidayPath);

        foreach(array_slice($holidayData[0],1) as $eachHoliday) {
            $month = trim($eachHoliday[0]);
            $formattedHolidayDate = $currentYear."-".date("m", strtotime(trim("$eachHoliday[0] 1 2022")))."-".trim($eachHoliday[1]);
            if($this->checkRegion($empId,trim($eachHoliday[4]))){
                if(array_key_exists($month,$formattedHolidayData)) {
                    $appendData = $formattedHolidayData[$month];
                    array_push($appendData, ['day' => trim($eachHoliday[1]), 'type' => trim($eachHoliday[3]), 'reason' => trim($eachHoliday[2])]);
                    $formattedHolidayData[$month] = $appendData;
                } else {
                    $holidayByDay = [];
                    array_push($holidayByDay, ['day' => trim($eachHoliday[1]), 'type' => trim($eachHoliday[3]), 'reason' => trim($eachHoliday[2])]);
                    $formattedHolidayData[$month] = $holidayByDay;
                }
            }
        }
        return $formattedHolidayData;
    }

    public function getEmployeeLeaveDetails($fetchType, $year, $empId) {
        $formattedLeaveData = [];
        $leaveFilePath = config('constants.leave_list_base_path')."/".$year."/".config('constants.leave_list_excel_name');
        if(!file_exists($leaveFilePath)){
            return 'file does not exist';
        }
        $leaveData = Excel::toArray([],$leaveFilePath);
        $currentYear = Carbon::now()->format('Y');
        $today = Carbon::today()->modify('now');

        if($year == $currentYear) {
            foreach(array_slice($leaveData[0],1) as $eachLeave) {
                if($eachLeave[0] != null && trim($eachLeave[0]) == $empId) {
                    $validRecord = false;
                    $startDate = Carbon::parse($this->formatDate($eachLeave[4]));
                    $endDate = Carbon::parse($this->formatDate($eachLeave[5]));
    
                    $month = Carbon::parse($this->formatDate($eachLeave[4]))->format('F');
    
                    $startDay = Carbon::parse($this->formatDate($eachLeave[4]))->format('d');
                    $endDay = Carbon::parse($this->formatDate($eachLeave[5]))->format('d');
    
                    $day = $startDay;
                    if($startDate->ne($endDate)) {
                        $day = $day."-".$endDay;
                    }
                    
                    if($fetchType == 'history') {
                        if($today->gt($endDate)) {
                            $validRecord = true;
                        }
                    } else {
                        if($startDate->gte($today) || $endDate->gte($today)) {
                            $validRecord = true;
                        }
                    }
                    
                    if($validRecord) {
                        if(array_key_exists($month,$formattedLeaveData)) {
                            $appendByLeave = $formattedLeaveData[$month];
                            array_push($appendByLeave, ['day' => $day, 'type' => trim($eachLeave[2]), 'reason' => trim($eachLeave[3])]);
                            $formattedLeaveData[$month] = $appendByLeave;
                        }else {
                            $leaveByEntry = [];
                            array_push($leaveByEntry, ['day' => $day, 'type' => trim($eachLeave[2]), 'reason' => trim($eachLeave[3])]);
                            $formattedLeaveData[$month] = $leaveByEntry;
                        }
                    }
                }
            }
        }else {
            foreach(array_slice($leaveData[0],1) as $eachLeave) {
                if($eachLeave[0] != null && trim($eachLeave[0]) == $empId) {
                    $startDate = Carbon::parse($this->formatDate($eachLeave[4]));
                    $endDate = Carbon::parse($this->formatDate($eachLeave[5]));
    
                    $month = Carbon::parse($this->formatDate($eachLeave[4]))->format('F');
    
                    $startDay = Carbon::parse($this->formatDate($eachLeave[4]))->format('d');
                    $endDay = Carbon::parse($this->formatDate($eachLeave[5]))->format('d');
    
                    $day = $startDay;
                    if($startDate->ne($endDate)) {
                        $day = $day."-".$endDay;
                    }
    
                    if(array_key_exists($month,$formattedLeaveData)) {
                        $appendByLeave = $formattedLeaveData[$month];
                        array_push($appendByLeave, ['day' => $day, 'type' => trim($eachLeave[2]), 'reason' => trim($eachLeave[3])]);
                        $formattedLeaveData[$month] = $appendByLeave;
                    }else {
                        $leaveByEntry = [];
                        array_push($leaveByEntry, ['day' => $day, 'type' => trim($eachLeave[2]), 'reason' => trim($eachLeave[3])]);
                        $formattedLeaveData[$month] = $leaveByEntry;
                    }
                }
            }
        }

        // if($fetchType == 'upcoming'){
        //     $formattedLeaveData = array_merge_recursive($formattedLeaveData, $this->getHolidayListByCountry($empId));
        // }

        return $formattedLeaveData;
    }   

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
