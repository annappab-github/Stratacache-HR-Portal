<?php

namespace App\Http\Controllers;

use App\Models\Employment_type;
use Illuminate\Http\Request;

class EmploymentTypeController extends Controller
{
    const GET_ALL_EMPLOYEMENT_TYPES = [self::class, 'getAllEmployementTypes'];

    public function getAllEmployementTypes() {
        $types = Employment_type::get(['id', 'type']);
        return response()->json(
            $types
        , 200);
    }
}
