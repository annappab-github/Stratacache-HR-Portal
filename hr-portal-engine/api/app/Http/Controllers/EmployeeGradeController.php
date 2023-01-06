<?php

namespace App\Http\Controllers;

use App\Models\Employee_grade;
use Illuminate\Http\Request;

class EmployeeGradeController extends Controller
{
    const GET_ALL_GRADES = [self::class, 'getAllGrades'];

    public function getAllGrades() {
        $grades = Employee_grade::get(['id', 'grade']);
        return response()->json(
            $grades
        , 200);
    }
}
