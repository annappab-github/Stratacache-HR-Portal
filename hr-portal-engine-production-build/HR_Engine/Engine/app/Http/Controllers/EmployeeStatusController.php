<?php

namespace App\Http\Controllers;

use App\Models\Employee_status;
use Illuminate\Http\Request;

class EmployeeStatusController extends Controller
{
    const GET_ALL_STATUS = [self::class, 'getAllStatus'];

    public function getAllStatus() {
        $status = Employee_status::get(['id', 'status']);
        return response()->json(
            $status
        , 200);
    }
}
