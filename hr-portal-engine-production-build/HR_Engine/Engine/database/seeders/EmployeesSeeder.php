<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Excel;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Employees;
use Illuminate\Support\Facades\Log;


class EmployeesSeeder extends Seeder
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
    
    public function handleConfirmationDate($startDate,$probationMonths){  
        if($probationMonths != null) {
            $date = strtotime($startDate); 
            $formattedDate = date("Y-m-d", strtotime("+".(string) $probationMonths." month", $date));
            return $formattedDate;
        }
    }

    public function convertEmployeeProfilePhoto($empId){
        $imgPath = config('constants.employee_photo_path');
        foreach(glob(''.$imgPath.'/*.*') as $filename){
        $imageName = basename($filename,".png");
            if($empId == $imageName){
                $path = $filename; 
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            return $base64;
            }
        }
    }

    public function handleBirthDateAndNewConfirmationDate($date){
        if($date != null) {
            $formattedDate = Carbon::parse($this->formatDate($date)); 
            return $formattedDate;
        }
    }
   
    public function run()
    {
        $eachEmployee = Employees::create([
            'empid' => 'ADM01',
            'position' => 'admin',
            'display_to_employees' => 0
        ]);
        $eachEmployee = Employees::create([
            'empid' => 'HRMAUDIT01',
            'position' => 'auditor',
            'display_to_employees' => 0
        ]);
        $excelPath = config('constants.employee_list_excel_path');
        $excelData = Excel::toArray([],$excelPath);
        foreach(array_slice($excelData[0],1) as $eachData) {
            $startDate = Carbon::parse($this->formatDate($eachData[2]));
            $birthDate = $this->handleBirthDateAndNewConfirmationDate($eachData[12]);
            $profilePic = $this->convertEmployeeProfilePhoto($eachData[1]);
            $confirmationDate = Carbon::parse($this->handleConfirmationDate($startDate,$eachData[13]));
            $newConfirmationdate = $this->handleBirthDateAndNewConfirmationDate($eachData[16]);

            if($eachData[1] != null) {
                $eachEmployee = Employees::create([
                    'empid' => trim($eachData[1]),
                    'start_Date' => $startDate,
                    'employee_grade' => trim($eachData[3]),
                    'location' => trim($eachData[5]),
                    'position' => trim($eachData[6]),
                    'department' => trim($eachData[7]),
                    'land_ext' => trim($eachData[8]),
                    'mobile_number' => trim($eachData[9]),
                    'reporting_to' => trim($eachData[11]),
                    'birth_dates' => $birthDate,
                    'profile_pic' => $profilePic,
                    'probation_period' => trim($eachData[13]),
                    'confirmation_date' => $confirmationDate,
                    'remarks' => trim($eachData[15]),
                    'new_confirmationdate' => $newConfirmationdate,
                    'city' => trim($eachData[17]),
                    'employement_type' => trim($eachData[18])
                ]);
            }
            // Log::debug($eachEmployee);
        }
    }
}
