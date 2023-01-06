<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employees extends Model
{
    use HasFactory;

    protected $fillable = [
        'empid',
        'start_Date',
        'employee_grade',
        // 'employee_name',
        'location',
        'position',
        'department',
        'land_ext',
        'mobile_number',
        // 'email',
        'reporting_to',
        'birth_dates',
        'probation_period',
        'confirmation_date',
        'remarks',
        'new_confirmationdate',
        'active'
    ];
}
