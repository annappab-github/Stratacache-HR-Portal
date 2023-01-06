<?php

namespace App\Http\Controllers;

use App\Models\Departments;
use Illuminate\Http\Request;

class DepartmentsController extends Controller
{
    const GET_ALL_DEPERMENTS = [self::class, 'getAllDepertments'];

    public function getAllDepertments() {
        $departments = Departments::get(['id', 'deparment']);
        return response()->json(
            $departments
        , 200);
    }
}
