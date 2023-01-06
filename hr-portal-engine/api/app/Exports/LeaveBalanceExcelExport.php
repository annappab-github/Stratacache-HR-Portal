<?php

namespace App\Exports;

use App\Invoice;
use App\Models\Gate;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\ReportController;
use App\Exports\ALSLBalanceExcelExport;
use App\Exports\ALDetailedExcelExport;
use App\Exports\SLDetailedExcelExport;

class LeaveBalanceExcelExport implements WithMultipleSheets
{
    public function sheets(): array 
    {   
        return [
            new ALSLBalanceExcelExport(),
            new ALDetailedExcelExport(),
            new SLDetailedExcelExport(),
        ];
    }
}